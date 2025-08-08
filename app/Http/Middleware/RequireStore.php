<?php

namespace App\Http\Middleware;

use App\Services\CurrentStore;
use Closure;
use Illuminate\Http\Request;

class RequireStore
{
    public function handle(Request $request, Closure $next)
    {
        if (!CurrentStore::has()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'STORE_REQUIRED',
                    'message' => "Debe especificar la tienda mediante el header 'X-Store-Id' (o 'Store-Id')",
                ]
            ], 400);
        }

        return $next($request);
    }
}
