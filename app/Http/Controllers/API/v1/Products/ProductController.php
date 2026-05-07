<?php

namespace App\Http\Controllers\API\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * List products, optionally filtered by company_id or category_id.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with(['company', 'category', 'variants'])
            ->when(
                $request->filled('company_id'),
                fn ($query) => $query->where('company_id', $request->integer('company_id'))
            )
            ->when(
                $request->filled('category_id'),
                fn ($query) => $query->where('category_id', $request->integer('category_id'))
            )
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product and its variants inside a DB transaction.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = DB::transaction(function () use ($request): Product {
            /** @var Product $product */
            $product = Product::create([
                'company_id' => $request->integer('company_id'),
                'category_id' => $request->integer('category_id'),
                'name' => $request->string('name'),
                'description' => $request->input('description'),
                'base_image' => $request->input('base_image'),
                'is_active' => $request->boolean('is_active', true),
                'attributes' => $request->input('attributes'),
            ]);

            $variantData = collect($request->input('variants'))
                ->map(fn (array $variant) => [
                    'sku' => $variant['sku'],
                    'price' => $variant['price'],
                    'stock_quantity' => $variant['stock_quantity'],
                    'variant_attributes' => $variant['variant_attributes'] ?? null,
                ])
                ->all();

            $product->variants()->createMany($variantData);

            return $product->load(['company', 'category', 'variants']);
        });

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single product with its category, company, and variants.
     */
    public function show(Product $product): ProductResource
    {
        $product->load(['company', 'category', 'variants']);

        return new ProductResource($product);
    }

    /**
     * Update a product's core fields (variants managed separately via ProductVariantController).
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        $product->load(['company', 'category', 'variants']);

        return new ProductResource($product);
    }

    /**
     * Delete a product and cascade-delete all its variants (handled by DB constraint).
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
