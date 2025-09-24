<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilterService
{
    /**
     * Aplicar filtros a la consulta de productos
     */
    public function applyFilters(Builder $query, Request $request): Builder
    {
        if (!$request->has('filters')) {
            return $query;
        }

        $filters = $request->get('filters');

        // Filtro por nombre del producto
        if (isset($filters['name']) && !empty($filters['name'])) {
            $query->where('name', 'ILIKE', '%' . $filters['name'] . '%');
        }

        // Filtro por slug del producto
        if (isset($filters['slug']) && !empty($filters['slug'])) {
            $query->where('slug', 'ILIKE', '%' . $filters['slug'] . '%');
        }

        // Filtro por SKU del producto
        if (isset($filters['sku']) && !empty($filters['sku'])) {
            $query->where('sku', 'ILIKE', '%' . $filters['sku'] . '%');
        }

        // Filtro por nombre de variante
        if (isset($filters['variantName']) && !empty($filters['variantName'])) {
            $query->whereHas('variants', function($variantQuery) use ($filters) {
                $variantQuery->where('variant_name', 'ILIKE', '%' . $filters['variantName'] . '%');
            });
        }

        return $query;
    }

    /**
     * Aplicar inclusiones (relationships) a la consulta
     */
    public function applyIncludes(Builder $query, Request $request, array $validIncludes = []): Builder
    {
        if (!$request->has('include')) {
            return $query;
        }

        $includes = explode(',', $request->get('include'));

        foreach ($includes as $include) {
            if (in_array($include, $validIncludes)) {
                $query->with($include);
            }
        }

        return $query;
    }
} 