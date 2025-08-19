<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariantRequest;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use App\Services\CurrentStore;
use App\Http\Resources\ProductVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantsController extends Controller
{
    
    public function index(Request $request): JsonResource
    {
        $variants = ProductVariant::all();

        return ProductVariantResource::collection($variants);
    }


    public function store(VariantRequest $request): JsonResponse
    {
        $data = $request->validated();

        $variant = ProductVariant::create($data);

        return response()->json([
            'message' => 'Variant created successfully',
            'variant' => $variant,
        ], 201);
        
    }

    public function show(string $id): JsonResource
    {
        $variant = ProductVariant::findOrFail($id);
        
        return new ProductVariantResource($variant);
    }

    public function update(VariantRequest $request, string $id): JsonResponse
    {
        $variant = ProductVariant::findOrFail($id);
        
        $data = $request->validated();
        $variant->update($data);
        
        return response()->json([
            'message' => 'Variant updated successfully',
            'variant' => $variant,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        
        return response()->json([
            'message' => 'Variant deleted successfully',
        ]);
    }
    
}
