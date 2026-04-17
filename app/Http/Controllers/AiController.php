<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{

    private string $baseUrl = 'https://router.huggingface.co/hf-inference/models/';
    public function summarize(Request $request)
    {

        $data = $request->validate([
            'text' => 'required|string|min:30',
            'model' => 'nullable|string'
        ]);

        $model = $data['model'] ?? 'facebook/bart-large-cnn';

        $body = $this->callHuggingFace($model, [
            'inputs' => $data['text'],
            'parameters' => [
                'max_length' => 130,
                'min_length' => 30,
                'do_sample' => false,
            ],
        ]);

        if ($body instanceof \Illuminate\Http\JsonResponse) {
            return $body;
        }

        $summary = data_get($body, '0.summary_text');

        if (!$summary) {
            Log::error('[AI] Unexpected summary response', ['body' => $body]);

            return response()->json([
                'error' => 'Invalid response from summarization model',
                'detail' => $body
            ], 500);
        }

        return response()->json([
            'summary' => $summary
        ]);
    }


private function callHuggingFace(string $model, array $payload, int $timeout = 60)
    {
        $apiKey = env('HUGGINGFACE_KEY');
        $baseUrl = env('APP_URL');

        if (!$apiKey) {
            Log::error('[AI] Missing HuggingFace API key.');
            return response()->json(['error' => 'API key not configured'], 500);
        }

        $url = $this->baseUrl . $model;

        $response = Http::withToken($apiKey)
            ->timeout($timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        Log::info('[AI] HuggingFace response', [
            'model' => $model,
            'status' => $response->status()
        ]);

        if ($response->status() === 503) {
            return response()->json(['error' => 'MODEL_LOADING'], 503);
        }

        if ($response->status() === 401) {
            return response()->json(['error' => 'Invalid HuggingFace API key'], 401);
        }

        if ($response->failed()) {
            return response()->json([
                'error' => 'HuggingFace API error',
                'detail' => $response->json('error') ?? $response->body(),
                'status' => $response->status(),
            ], 500);
        }

        $body = $response->json();

        if (isset($body['error'])) {
            return response()->json([
                'error' => $body['error'],
                'detail' => $body,
            ], 500);
        }

        return $body;
    }


}
