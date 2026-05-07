<?php

namespace App\Http\Controllers\API\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * List all categories, optionally with their children tree.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->with('children')
            ->when(
                $request->boolean('flat'),
                fn ($query) => $query->without('children'),
            )
            ->whereNull('parent_id')
            ->latest()
            ->paginate(20);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        $category->load('parent');

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single category with its parent and immediate children.
     */
    public function show(Category $category): CategoryResource
    {
        $category->load(['parent', 'children']);

        return new CategoryResource($category);
    }

    /**
     * Update a category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        $category->load(['parent', 'children']);

        return new CategoryResource($category);
    }

    /**
     * Delete a category.
     * Children will have their parent_id set to null (nullOnDelete constraint).
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
