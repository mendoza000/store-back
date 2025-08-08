<?php

namespace App\Http\Middleware;

use App\Models\Store;
use App\Services\CurrentStore;
use Closure;
use Illuminate\Http\Request;

class ResolveStore
{
    public function handle(Request $request, Closure $next)
    {
        // Soporta header 'X-Store-Id' o 'Store-Id'
        $storeId = $request->header('X-Store-Id') ?? $request->header('Store-Id');

        if ($storeId) {
            $store = Store::query()->find($storeId);
            if ($store) {
                CurrentStore::set($store);
            }
        }

        // También permitir resolver por subdominio futuro o query param 'store_id'
        if (!CurrentStore::has() && $request->query('store_id')) {
            $store = Store::query()->find($request->query('store_id'));
            if ($store) {
                CurrentStore::set($store);
            }
        }

        return $next($request);
    }
}
