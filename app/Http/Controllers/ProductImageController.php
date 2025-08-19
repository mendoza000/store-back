<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ImageRequest;


class ProductImageController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $images = ProductImage::all();
        return response()->json($images);
    }


    public function store(ImageRequest $request): JsonResponse
    {
        // Logic to store a new product image
        $image = ProductImage::create($request->validated());

        return response()->json([
            'message' => 'Product image created successfully',
            'data' => $image
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        // Logic to show a specific product image
        $image = ProductImage::findOrFail($id);

        return response()->json([
            'data' => $image
        ]);
    }

    public function update(ImageRequest $request, string $id): JsonResponse
    {
        // Logic to update a specific product image
        $image = ProductImage::findOrFail($id);
        $image->update($request->validated());

        return response()->json([
            'message' => 'Product image updated successfully',
            'data' => $image
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        // Logic to delete a specific product image
        $image = ProductImage::findOrFail($id);
        $image->delete();

        return response()->json([
            'message' => 'Product image deleted successfully'
        ]);
    }

    
}
