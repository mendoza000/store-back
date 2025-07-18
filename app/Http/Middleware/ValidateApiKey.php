<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): ResponseAlias
    {
        $apiKey = $this->extractApiKey($request);

        if (!$apiKey) {
            return $this->createMissingApiKeyResponse();
        }

        if (!$this->isValidApiKey($apiKey)) {
            return $this->createInvalidApiKeyResponse();
        }

        return $next($request);
    }

    /**
     * Extract API key from request
     */
    private function extractApiKey(Request $request): ?string
    {
        // Try different sources for API key
        return $request->header('X-API-Key')
            ?? $request->header('Authorization-Bearer')
            ?? $request->query('api_key')
            ?? $request->bearerToken();
    }

    /**
     * Validate API key against configured keys
     */
    private function isValidApiKey(string $apiKey): bool
    {
        $validKeys = $this->getValidApiKeys();

        return in_array($apiKey, $validKeys, true);
    }

    /**
     * Get valid API keys from configuration
     */
    private function getValidApiKeys(): array
    {
        $configKeys = config('modules.api_keys', []);
        $envKeys = array_filter([
            config('app.api_key'),
            env('API_KEY'),
            env('EXTERNAL_API_KEY'),
        ]);

        return array_merge($configKeys, $envKeys);
    }

    /**
     * Create response for missing API key
     */
    private function createMissingApiKeyResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'API_KEY_MISSING',
                'message' => 'API key is required for this endpoint',
                'details' => [
                    'accepted_headers' => [
                        'X-API-Key',
                        'Authorization: Bearer {key}',
                    ],
                    'accepted_query_params' => ['api_key']
                ]
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Create response for invalid API key
     */
    private function createInvalidApiKeyResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'API_KEY_INVALID',
                'message' => 'The provided API key is invalid',
            ]
        ], Response::HTTP_FORBIDDEN);
    }
}
