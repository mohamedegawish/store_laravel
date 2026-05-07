<?php

namespace App\Http\Controllers\API\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends Controller
{
    /**
     * List all variants for a given product.
     */
    public function index(Product $product): AnonymousResourceCollection
    {
        return ProductVariantResource::collection(
            $product->variants()->latest()->paginate(20)
        );
    }

    /**
     * Add a new variant to a product.
     */
    public function store(StoreProductVariantRequest $request, Product $product): JsonResponse
    {
        $variant = $product->variants()->create($request->validated());

        return (new ProductVariantResource($variant))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single variant (scoped to its parent product).
     */
    public function show(Product $product, ProductVariant $variant): ProductVariantResource
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        return new ProductVariantResource($variant);
    }

    /**
     * Update a variant (scoped to its parent product).
     */
    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ): ProductVariantResource {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->update($request->validated());

        return new ProductVariantResource($variant);
    }

    /**
     * Delete a variant (scoped to its parent product).
     */
    public function destroy(Product $product, ProductVariant $variant): JsonResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->delete();

        return response()->json(['message' => 'Variant deleted successfully.']);
    }

    /**
     * Abort with 404 if the variant does not belong to the given product.
     */
    private function ensureVariantBelongsToProduct(Product $product, ProductVariant $variant): void
    {
        abort_if($variant->product_id !== $product->id, 404, 'Variant not found for this product.');
    }
}
