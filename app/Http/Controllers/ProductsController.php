<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductsRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;
use App\Services\CurrentStore;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        // El StoreScope del trait BelongsToStore ya filtra por tienda automáticamente.
        // Paginamos para evitar respuestas enormes.
        $products = Product::query()->paginate(15);
        return ProductResource::collection($products);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductsRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Asegurar que el producto quede asociado a la tienda del header
        unset($data['store_id']);
        $data['store_id'] = CurrentStore::id();

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {

        $product = Product::findOrFail($id);

        return response()->json([
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductsRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();
        // Evitar cambios de tienda por el body
        unset($data['store_id']);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 204);
    }
}
