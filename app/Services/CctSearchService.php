<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CctSearchService
{
    public function __construct(
        protected GeminiEmbeddingService $embeddingService
    ) {}

    /**
     * Realiza una búsqueda semántica sobre los fragmentos del CCT.
     *
     * @param string $query
     * @param int $limit
     * @param string|null $capitulo
     * @return array<int, array{id: int, score: float, capitulo: string, clausula: ?string, tema: ?string, contenido: string}>
     */
    public function search(string $query, int $limit = 4, ?string $capitulo = null): array
    {
        $queryVector = $this->embeddingService->embed($query);

        $chunksQuery = DB::table('cct_chunks')
            ->select(['id', 'capitulo', 'clausula', 'tema', 'contenido', 'embedding']);

        if ($capitulo !== null && trim($capitulo) !== '') {
            $chunksQuery->where('capitulo', $capitulo);
        }

        $chunks = $chunksQuery->get();

        $results = [];

        foreach ($chunks as $chunk) {
            $chunkVector = is_string($chunk->embedding)
                ? json_decode($chunk->embedding, true)
                : (array) $chunk->embedding;

            if (!is_array($chunkVector) || empty($chunkVector)) {
                continue;
            }

            $score = $this->cosineSimilarity($queryVector, $chunkVector);

            $results[] = [
                'id' => $chunk->id,
                'score' => $score,
                'capitulo' => $chunk->capitulo,
                'clausula' => $chunk->clausula,
                'tema' => $chunk->tema,
                'contenido' => $chunk->contenido,
            ];
        }

        usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Calcula la similitud coseno entre dos vectores numéricos.
     *
     * @param float[] $vecA
     * @param float[] $vecB
     * @return float
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $count = min(count($vecA), count($vecB));

        if ($count === 0) {
            return 0.0;
        }

        for ($i = 0; $i < $count; $i++) {
            $valA = (float) $vecA[$i];
            $valB = (float) $vecB[$i];
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}

