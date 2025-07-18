<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): ResponseAlias
    {
        $user = $request->user();

        if (!$user) {
            return $this->createUnauthorizedResponse('Authentication required');
        }

        if (!$this->isAdmin($user)) {
            return $this->createForbiddenResponse();
        }

        return $next($request);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        // This can be expanded based on your user role system
        return $user->role === 'admin' || $user->is_admin ?? false;
    }

    /**
     * Create unauthorized response
     */
    private function createUnauthorizedResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
                'message' => $message,
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Create forbidden response
     */
    private function createForbiddenResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'INSUFFICIENT_PERMISSIONS',
                'message' => 'Administrator access required',
                'details' => [
                    'required_role' => 'admin',
                ]
            ]
        ], Response::HTTP_FORBIDDEN);
    }
}
