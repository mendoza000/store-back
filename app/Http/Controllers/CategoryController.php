<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryController extends Controller
{
    
    public function index(): JsonResource
    {
        // Logic to list all categories

        return CategoryResource::collection(Category::all());
    }


    public function store(CategoryRequest $request): JsonResponse
    {
        // Logic to create a new category
        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
        
    }


    public function show(string $id): JsonResponse
    {
        // Logic to show a specific category
        $category = Category::findOrFail($id);

        return response()->json([
            'data' => new CategoryResource($category)
        ]);
    }

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


    public function destroy(string $id): JsonResponse
    {
        // Logic to delete a specific category
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }


}
