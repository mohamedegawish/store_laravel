<?php

namespace App\Http\Controllers\API\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ProductVariantController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        Gate::authorize('view', $product);

        return ProductVariantResource::collection(
            $product->variants()->latest()->paginate(20)
        );
    }

    public function store(StoreProductVariantRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        $variant = $product->variants()->create($request->validated());

        return (new ProductVariantResource($variant))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product, ProductVariant $variant): ProductVariantResource
    {
        Gate::authorize('view', $product);
        $this->ensureVariantBelongsToProduct($product, $variant);

        return new ProductVariantResource($variant);
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ): ProductVariantResource {
        Gate::authorize('update', $product);
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->update($request->validated());

        return new ProductVariantResource($variant);
    }

    public function destroy(Product $product, ProductVariant $variant): JsonResponse
    {
        Gate::authorize('update', $product);
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->delete();

        return response()->json(['message' => 'Variant deleted successfully.']);
    }

    private function ensureVariantBelongsToProduct(Product $product, ProductVariant $variant): void
    {
        abort_if($variant->product_id !== $product->id, 404, 'Variant not found for this product.');
    }
}
