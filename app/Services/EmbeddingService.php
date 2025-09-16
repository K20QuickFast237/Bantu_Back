<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    public static function getEmbedding(string $text): array
    {
        $response = Http::post(env('PYTHON_EMBEDDING_URL') . '/embed', [
            'text' => $text,
        ]);

        return $response->json('embedding', []);
    }
}
