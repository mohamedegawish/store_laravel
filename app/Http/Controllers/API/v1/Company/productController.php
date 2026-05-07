<?php

namespace App\Http\Controllers\API\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with(['category', 'variants'])
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        // Enforce company ID from auth, ignore request
        $companyId = auth()->user()->company_id;

        $product = DB::transaction(function () use ($request, $companyId): Product {
            $product = Product::create([
                'company_id' => $companyId,
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

            return $product->load(['category', 'variants']);
        });

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        Gate::authorize('view', $product);

        $product->load(['category', 'variants']);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        Gate::authorize('update', $product);

        $data = $request->validated();
        unset($data['company_id']); // Cannot transfer ownership

        $product->update($data);
        $product->load(['category', 'variants']);

        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
