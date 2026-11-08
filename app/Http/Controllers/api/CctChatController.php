<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CctQueryRequest;
use App\Services\CctRagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CctChatController extends Controller
{
    public function __construct(
        protected CctRagService $ragService
    ) {}

    /**
     * Procesa una consulta en lenguaje natural sobre el CCT IMSS-SNTSS (con soporte multi-turn).
     */
    public function preguntar(CctQueryRequest $request): JsonResponse
    {
        try {
            $pregunta = $request->validated('pregunta');
            $sessionId = $request->validated('session_id');
            $limite = (int) ($request->validated('limite') ?? 4);
            $refresh = $request->boolean('refresh', false);
            $userIdentifier = $request->user()?->id ? (string) $request->user()->id : $request->ip();

            $resultado = $this->ragService->ask(
                $pregunta,
                $sessionId,
                $limite,
                $refresh,
                $userIdentifier
            );

            return response()->json($resultado, 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la consulta sobre el CCT.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Devuelve la sesión y el historial completo de mensajes para una conversación dada.
     */
    public function verSesion(string $session): JsonResponse
    {
        $chatSession = DB::table('cct_chat_sessions')->where('id', $session)->first();

        if (!$chatSession) {
            return response()->json([
                'success' => false,
                'message' => 'La sesión de conversación solicitada no existe.',
            ], 404);
        }

        $mensajes = DB::table('cct_chat_messages')
            ->where('session_id', $session)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'role'       => $msg->role,
                    'content'    => $msg->content,
                    'fuentes'    => $msg->fuentes ? json_decode($msg->fuentes, true) : null,
                    'created_at' => $msg->created_at,
                ];
            });

        return response()->json([
            'session_id'      => $chatSession->id,
            'titulo'          => $chatSession->titulo,
            'user_identifier' => $chatSession->user_identifier,
            'created_at'      => $chatSession->created_at,
            'updated_at'      => $chatSession->updated_at,
            'mensajes'        => $mensajes,
        ], 200);
    }
}
