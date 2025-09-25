<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

/**
 * Controlador para gestión de categorías
 * 
 * @see App\OpenApi\Documentation\CategoryEndpoints Para documentación de endpoints
 */
class CategoryController extends Controller
{
    
    /**
     * Lista de categorías activas paginadas
     * 
     * @see App\OpenApi\Documentation\CategoryEndpoints::index() Para documentación OpenAPI
     */
    public function index(): JsonResource
    {
        $categories = Category::active()->paginate(20);

        return CategoryResource::collection($categories);
    }

    /**
     * Crear nueva categoría
     * 
     * @see App\OpenApi\Documentation\CategoryEndpoints::store() Para documentación OpenAPI
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
        
    }

    /**
     * Mostrar categoría por ID
     * 
     * @see App\OpenApi\Documentation\CategoryEndpoints::show() Para documentación OpenAPI
     */
    public function show(string $id): JsonResponse
    {
        // Logic to show a specific category
        $category = Category::findOrFail($id);

        return response()->json([
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Actualizar categoría existente
     * 
     * @see App\OpenApi\Documentation\CategoryEndpoints::update() Para documentación OpenAPI
     */
    public function update(CategoryRequest $request, string $id): JsonResponse
    {
        // Logic to update a specific category
        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Eliminar categoría
     * 
     * @see App\OpenApi\Documentation\CategoryEndpoints::destroy() Para documentación OpenAPI
     */
    public function destroy(string $id): JsonResponse
    {
        // Logic to delete a specific category
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Mostrar detalle de categoría por slug
     * GET /api/v1/categories/{slug}
     * 
     * @param string $slug
     * @return JsonResponse
     * @see App\OpenApi\Documentation\CategoryEndpoints::showBySlug() Para documentación OpenAPI
     */
    public function showBySlug(string $slug): JsonResponse
    {
        // Buscar categoría activa por slug
        $category = Category::active()
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CATEGORY_NOT_FOUND',
                    'message' => 'Categoría no encontrada',
                    'details' => [
                        'slug' => $slug
                    ]
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Obtener productos de una categoría por slug
     * GET /api/v1/categories/{slug}/products
     * 
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     * @see App\OpenApi\Documentation\CategoryEndpoints::getProductsBySlug() Para documentación OpenAPI
     */
    public function getProductsBySlug(Request $request, string $slug): JsonResponse
    {
        // Buscar categoría activa por slug
        $category = Category::active()
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CATEGORY_NOT_FOUND',
                    'message' => 'Categoría no encontrada',
                    'details' => [
                        'slug' => $slug
                    ]
                ]
            ], 404);
        }

        // Obtener parámetros de consulta
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status', 'active');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        // Construir consulta de productos
        $query = $category->products()
            ->where('status', $status)
            ->with(['images', 'variants']);

        // Aplicar filtros opcionales
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('short_description', 'like', '%' . $search . '%');
            });
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Aplicar ordenamiento
        $allowedSortFields = ['name', 'price', 'created_at', 'stock'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Obtener productos paginados
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'products' => ProductResource::collection($products->items()),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ]
        ]);
    }


}
