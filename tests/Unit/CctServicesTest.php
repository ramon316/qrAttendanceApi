<?php

namespace Tests\Unit;

use App\Services\CctSearchService;
use App\Services\GeminiEmbeddingService;
use Illuminate\Support\Facades\Http;
use ReflectionMethod;
use Tests\TestCase;

class CctServicesTest extends TestCase
{
    public function test_services_can_be_instantiated_and_configured(): void
    {
        $embeddingService = app(GeminiEmbeddingService::class);
        $searchService = app(CctSearchService::class);

        $this->assertInstanceOf(GeminiEmbeddingService::class, $embeddingService);
        $this->assertInstanceOf(CctSearchService::class, $searchService);
    }

    public function test_embedding_service_returns_768_float_vector(): void
    {
        config([
            'services.google_gemini.api_key' => 'fake-api-key',
            'services.google_gemini.model_embedding' => 'text-embedding-004',
        ]);

        $fakeVector = array_fill(0, 768, 0.125);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'embedding' => [
                    'values' => $fakeVector,
                ],
            ], 200),
        ]);

        $service = app(GeminiEmbeddingService::class);
        $result = $service->embed('Cláusula de vacaciones');

        $this->assertCount(768, $result);
        $this->assertSame(0.125, $result[0]);
    }

    public function test_cosine_similarity_handles_normal_and_zero_vectors(): void
    {
        $searchService = app(CctSearchService::class);
        $method = new ReflectionMethod(CctSearchService::class, 'cosineSimilarity');
        $method->setAccessible(true);

        $this->assertEqualsWithDelta(1.0, $method->invoke($searchService, [1.0, 2.0, 3.0], [1.0, 2.0, 3.0]), 0.0001);
        $this->assertSame(0.0, $method->invoke($searchService, [0.0, 0.0], [1.0, 2.0]));
    }
}

