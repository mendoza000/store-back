<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductsRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request):JsonResource
    {
        $query = Product::query();

        if($request->has('include')) {
            $includes = explode(',', $request->get('include'));

            $validIncludes = ['images', 'category', 'variants'];

            foreach ($includes as $include) {
                if (in_array($include, $validIncludes)) {
                    $query->with($include);
                }
            }
        }
        
        // Apply filters and sorting before pagination
        $products = $query->active()
            ->orderBy('name')
            ->paginate(15);
        
        return ProductResource::collection($products);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductsRequest $request): JsonResponse
    {
        
        $product = Product::create($request->all());

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
        $product->update($request->all());

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
