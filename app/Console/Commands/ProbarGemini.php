<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ProbarGemini extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cct:probar-gemini';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la conexión con la API de Gemini Embeddings (modelo gemini-embedding-001)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Probando conexión con la API de Gemini Embeddings...');

        // 1. Obtener la API key desde config/services.php
        $apiKey = config('services.gemini.api_key') ?? config('services.gemini.key');

        if (empty($apiKey)) {
            $this->error('❌ Error: La variable GEMINI_API_KEY no está configurada en config/services.php ni en el archivo .env.');
            return self::FAILURE;
        }

        // 2. Definir texto de prueba y endpoint
        $testText = 'El trabajador tendrá derecho a licencia con goce de sueldo por matrimonio de 5 días hábiles';
        $model = 'gemini-embedding-001';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:embedContent";

        $this->line("📝 Texto de prueba: <comment>\"{$testText}\"</comment>");
        $this->line("🤖 Modelo: <comment>{$model}</comment>");
        $this->newLine();

        try {
            // Consultar la API de Gemini usando Http::withHeaders()
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'model' => "models/{$model}",
                'content' => [
                    'parts' => [
                        [
                            'text' => $testText,
                        ],
                    ],
                ],
            ]);

            // 3. Confirmar si la llamada fue exitosa e imprimir las dimensiones
            if ($response->successful()) {
                $values = $response->json('embedding.values') ?? [];
                $dimensionCount = count($values);

                $this->info('✅ ¡Llamada a la API de Gemini exitosa!');
                $this->info("📐 Dimensiones del vector devuelto: {$dimensionCount}");

                if ($dimensionCount > 0) {
                    $preview = array_slice($values, 0, 5);
                    $this->line('🔍 Muestra (primeros 5 valores): ' . json_encode($preview));
                }

                return self::SUCCESS;
            }

            $this->error("❌ La llamada a la API de Gemini falló con estado HTTP: {$response->status()}");
            $this->line($response->body());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('❌ Ocurrió una excepción al conectar con la API de Gemini: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
