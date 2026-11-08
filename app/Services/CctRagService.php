<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CctRagService
{
    private const MIN_SIMILARITY_SCORE = 0.25;
    private const MAX_HISTORY_MESSAGES = 8; // Últimos 4 turnos (usuario + asistente)

    public function __construct(
        protected CctSearchService $searchService
    ) {}

    /**
     * Normaliza la pregunta y genera la llave de caché determinista.
     * Incorpora sessionId y longitud de historial cuando existe conversación previa.
     */
    private function getCacheKey(string $pregunta, int $limit, ?string $sessionId = null, int $historyCount = 0): string
    {
        $normalized = mb_strtolower(trim($pregunta), 'UTF-8');
        $normalized = strtr($normalized, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        ]);
        $normalized = preg_replace('/[¿?¡!.,;:"]+/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        $suffix = "_limit_{$limit}";
        if ($historyCount > 0 && $sessionId !== null) {
            $suffix .= "_session_{$sessionId}_hist_{$historyCount}";
        }

        return 'cct_query_' . md5($normalized . $suffix);
    }

    /**
     * Reformula la pregunta actual en una consulta autónoma usando el historial reciente.
     *
     * @param string $preguntaActual
     * @param array<int, object> $historialPrevio
     * @return string
     */
    private function condenseQuery(string $preguntaActual, array $historialPrevio): string
    {
        if (empty($historialPrevio)) {
            return $preguntaActual;
        }

        $apiKey = config('services.google_gemini.api_key');
        $chatModel = config('services.google_gemini.model_chat', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            return $preguntaActual;
        }

        $systemPrompt = 'Eres un reformulador de consultas de búsqueda. Dado el historial de la conversación y una nueva pregunta que puede hacer referencia a lo dicho antes, genera una única pregunta independiente y específica sobre el Contrato Colectivo de Trabajo IMSS-SNTSS que capture todo el contexto necesario para una búsqueda documental. No respondas a la pregunta, solo reformúlala en una sola línea clara.';

        // Tomar los últimos 4 a 6 mensajes para contexto ligero
        $recentMessages = array_slice($historialPrevio, -6);
        $historyLines = [];
        foreach ($recentMessages as $msg) {
            $speaker = $msg->role === 'assistant' ? 'Asistente' : 'Usuario';
            // Recortar respuestas muy extensas en el reformulador para mantener baja latencia
            $text = mb_strlen($msg->content) > 600
                ? mb_substr($msg->content, 0, 600) . '...'
                : $msg->content;
            $historyLines[] = "{$speaker}: {$text}";
        }

        $historyBlock = implode("\n", $historyLines);
        $prompt = "HISTORIAL DE CONVERSACIÓN:\n{$historyBlock}\n\nNUEVA PREGUNTA DEL USUARIO:\n{$preguntaActual}\n\nPREGUNTA REFORMULADA INDEPENDIENTE:";

        $candidateModels = array_values(array_unique([
            $chatModel,
            'gemini-3.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-3.8-flash',
        ]));

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.0,
            ],
        ];

        foreach ($candidateModels as $modelToTry) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelToTry}:generateContent?key={$apiKey}";
                $response = Http::timeout(15)->post($url, $payload);

                if ($response->successful()) {
                    $condensed = $response->json('candidates.0.content.parts.0.text');
                    if (is_string($condensed) && trim($condensed) !== '') {
                        return trim($condensed);
                    }
                }
            } catch (ConnectionException) {
                continue;
            }
        }

        return $preguntaActual;
    }

    /**
     * Ejecuta el flujo RAG multi-turno con soporte de sesiones, reescritura contextual y caché.
     *
     * @param string $pregunta
     * @param string|null $sessionId
     * @param int $limitChunks
     * @param bool $forceRefresh
     * @param string|null $userIdentifier
     * @return array<string, mixed>
     */
    public function ask(
        string $pregunta,
        ?string $sessionId = null,
        int $limitChunks = 4,
        bool $forceRefresh = false,
        ?string $userIdentifier = null
    ): array {
        $apiKey = config('services.google_gemini.api_key');
        $chatModel = config('services.google_gemini.model_chat', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            throw new RuntimeException('La clave GEMINI_API_KEY no está configurada en services.google_gemini.api_key.');
        }

        // 1. Gestionar sesión en cct_chat_sessions
        $activeSessionId = ($sessionId !== null && trim($sessionId) !== '')
            ? trim($sessionId)
            : (string) Str::uuid();

        $sessionExists = DB::table('cct_chat_sessions')->where('id', $activeSessionId)->exists();

        if (!$sessionExists) {
            DB::table('cct_chat_sessions')->insert([
                'id'              => $activeSessionId,
                'user_identifier' => $userIdentifier,
                'titulo'          => Str::limit(trim($pregunta), 120),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } else {
            DB::table('cct_chat_sessions')
                ->where('id', $activeSessionId)
                ->update(['updated_at' => now()]);
        }

        // 2. Cargar últimos mensajes previos ordenados cronológicamente
        $historialPrevio = DB::table('cct_chat_messages')
            ->where('session_id', $activeSessionId)
            ->orderByDesc('id')
            ->limit(self::MAX_HISTORY_MESSAGES)
            ->get()
            ->reverse()
            ->values()
            ->all();

        $historyCount = count($historialPrevio);

        // 3. Verificar caché (evitando colisión si hay historial activo)
        $cacheKey = $this->getCacheKey($pregunta, $limitChunks, $activeSessionId, $historyCount);

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cachedResult = Cache::get($cacheKey);
            if (is_array($cachedResult)) {
                $this->persistTurnMessages(
                    $activeSessionId,
                    $pregunta,
                    $cachedResult['respuesta'],
                    $cachedResult['fuentes'] ?? []
                );

                $cachedResult['session_id'] = $activeSessionId;
                $cachedResult['cached'] = true;
                return $cachedResult;
            }
        }

        // 4. Reescritura contextual de la consulta si existe historial previo
        $consultaBusqueda = $this->condenseQuery($pregunta, $historialPrevio);

        // 5. Búsqueda vectorial usando la consulta reformulada
        $chunksRecuperados = $this->searchService->search($consultaBusqueda, $limitChunks);

        $chunksRelevantes = array_values(array_filter(
            $chunksRecuperados,
            fn(array $chunk) => $chunk['score'] >= self::MIN_SIMILARITY_SCORE
        ));

        if (empty($chunksRelevantes)) {
            $noInfoResponse = 'No se encontró información aplicable en el Contrato Colectivo de Trabajo (CCT IMSS-SNTSS) para responder a esta consulta.';

            $this->persistTurnMessages($activeSessionId, $pregunta, $noInfoResponse, []);

            return [
                'session_id'           => $activeSessionId,
                'pregunta'             => $pregunta,
                'pregunta_reformulada' => $consultaBusqueda,
                'respuesta'            => $noInfoResponse,
                'fuentes'              => [],
                'cached'               => false,
            ];
        }

        // 6. Concatenar los contenidos de los fragmentos recuperados
        $bloquesContexto = [];
        foreach ($chunksRelevantes as $idx => $chunk) {
            $num = $idx + 1;
            $clausula = $chunk['clausula'] ?? 'S/N';
            $tema = $chunk['tema'] ?? 'Sin tema especificado';
            $bloquesContexto[] = "--- FRAGMENTO #{$num} (Capítulo: {$chunk['capitulo']} | Cláusula: {$clausula} | Tema: {$tema}) ---\n{$chunk['contenido']}";
        }
        $contextoDocumental = implode("\n\n", $bloquesContexto);

        // 7. Formular System Instruction y construir array multi-turn de contents
        $systemInstruction = <<<EOT
Eres un asistente experto en normatividad laboral y en el Contrato Colectivo de Trabajo (CCT) celebrado entre el IMSS y el SNTSS.

Debes seguir de forma estricta las siguientes reglas:
1. Responder únicamente con base en el contexto documental proporcionado y el historial de la conversación.
2. Citar siempre de forma explícita el capítulo, número de cláusula o transitoria aplicable.
3. Mantener exactitud en plazos (días hábiles o naturales), montos, porcentajes y requisitos sin omitir ni extrapolar.
4. Si la consulta involucra cálculos por antigüedad (como vacaciones, fondo de ahorro o liquidaciones), desglosar la regla o tabla contractual respectiva.
5. Si la información no está contemplada en el contexto, indicar claramente que dicha estipulación no se localiza en los documentos recuperados.
EOT;

        $contents = [];
        foreach ($historialPrevio as $msg) {
            $contents[] = [
                'role'  => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [
                    ['text' => $msg->content],
                ],
            ];
        }

        $userPrompt = <<<EOT
CONTEXTO DOCUMENTAL DEL CONTRATO COLECTIVO DE TRABAJO (CCT IMSS-SNTSS):

{$contextoDocumental}

---
PREGUNTA DEL TRABAJADOR / USUARIO:
{$pregunta}
EOT;

        $contents[] = [
            'role'  => 'user',
            'parts' => [
                ['text' => $userPrompt],
            ],
        ];

        // 8. Llamada HTTP POST a Gemini con historial multi-turn
        $candidateModels = array_values(array_unique([
            $chatModel,
            'gemini-3.5-flash',
            'gemini-3.8-flash',
            'gemini-2.5-flash-lite',
        ]));

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature' => 0.2,
            ],
        ];

        $response = null;
        $lastException = null;
        foreach ($candidateModels as $modelToTry) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelToTry}:generateContent?key={$apiKey}";
                $response = Http::timeout(25)->post($url, $payload);

                if ($response->successful()) {
                    break;
                }

                if (!in_array($response->status(), [404, 429, 503], true)) {
                    break;
                }
            } catch (ConnectionException $e) {
                $lastException = $e;
                continue;
            }
        }

        if ($response === null || $response->failed()) {
            $detail = $response ? ('(' . $response->status() . '): ' . $response->body()) : ($lastException?->getMessage() ?? 'Sin respuesta');
            throw new RuntimeException('Error al generar respuesta en Gemini ' . $detail);
        }

        $textoGenerado = $response->json('candidates.0.content.parts.0.text');

        if (!is_string($textoGenerado) || trim($textoGenerado) === '') {
            throw new RuntimeException('La respuesta de Gemini no devolvió texto generado válido.');
        }

        $textoLimpio = trim($textoGenerado);

        $fuentes = array_map(fn($chunk) => [
            'capitulo' => $chunk['capitulo'],
            'clausula' => $chunk['clausula'],
            'tema'     => $chunk['tema'],
            'score'    => round($chunk['score'], 4),
        ], $chunksRelevantes);

        // 9. Persistir turno en cct_chat_messages
        $this->persistTurnMessages($activeSessionId, $pregunta, $textoLimpio, $fuentes);

        // 10. Construir resultado y guardar en caché
        $resultado = [
            'session_id'           => $activeSessionId,
            'pregunta'             => $pregunta,
            'pregunta_reformulada' => $consultaBusqueda,
            'respuesta'            => $textoLimpio,
            'fuentes'              => $fuentes,
            'cached'               => false,
        ];

        $ttlDays = (int) config('services.google_gemini.cache_ttl_days', 7);
        $expiresAt = now()->addDays($ttlDays);

        Cache::put($cacheKey, $resultado, $expiresAt);

        if (Cache::supportsTags()) {
            Cache::tags(['cct_queries'])->put($cacheKey, $resultado, $expiresAt);
        }

        $registeredKeys = Cache::get('cct_query_keys_registry', []);
        if (is_array($registeredKeys) && !in_array($cacheKey, $registeredKeys, true)) {
            $registeredKeys[] = $cacheKey;
            Cache::put('cct_query_keys_registry', $registeredKeys, $expiresAt);
        }

        return $resultado;
    }

    /**
     * Guarda el mensaje del usuario y la respuesta del asistente en cct_chat_messages.
     *
     * @param string $sessionId
     * @param string $pregunta
     * @param string $respuesta
     * @param array<int, array<string, mixed>> $fuentes
     */
    private function persistTurnMessages(string $sessionId, string $pregunta, string $respuesta, array $fuentes): void
    {
        $now = now();

        DB::table('cct_chat_messages')->insert([
            [
                'session_id' => $sessionId,
                'role'       => 'user',
                'content'    => $pregunta,
                'fuentes'    => null,
                'created_at' => $now,
            ],
            [
                'session_id' => $sessionId,
                'role'       => 'assistant',
                'content'    => $respuesta,
                'fuentes'    => !empty($fuentes) ? json_encode($fuentes) : null,
                'created_at' => $now,
            ],
        ]);
    }
}
