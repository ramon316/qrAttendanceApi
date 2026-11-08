<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiEmbeddingService
{
    /**
     * Genera un vector de embedding de 768 dimensiones para el texto proporcionado.
     *
     * @param string $text
     * @return float[]
     * @throws RuntimeException
     */
    public function embed(string $text): array
    {
        $apiKey = config('services.google_gemini.api_key');
        $model = config('services.google_gemini.model_embedding', 'gemini-embedding-001');

        if (empty($apiKey)) {
            throw new RuntimeException('La clave GEMINI_API_KEY no está configurada en services.google_gemini.api_key.');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:embedContent?key={$apiKey}";

        $response = Http::timeout(30)->post($url, [
            'model' => "models/{$model}",
            'content' => [
                'parts' => [
                    ['text' => $text],
                ],
            ],
            'outputDimensionality' => 768,
        ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Error al generar embedding con Google Gemini (' . $response->status() . '): ' . $response->body()
            );
        }

        $values = $response->json('embedding.values');

        if (!is_array($values) || empty($values)) {
            throw new RuntimeException(
                'La respuesta de Google Gemini no contiene valores válidos en embedding.values.'
            );
        }

        return array_map('floatval', $values);
    }

    /**
     * Alias de embed() para compatibilidad.
     *
     * @param string $text
     * @return float[]
     */
    public function generateEmbedding(string $text): array
    {
        return $this->embed($text);
    }

    /**
     * Alias de embed() para compatibilidad.
     *
     * @param string $text
     * @return float[]
     */
    public function getEmbedding(string $text): array
    {
        return $this->embed($text);
    }
}

