<?php

namespace App\Http\Controllers\API\V1\Store;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with(['company', 'category', 'variants'])
            ->where('is_active', true)
            ->when($request->query('category_id'), fn($q, $id) => $q->where('category_id', $id))
            ->latest()
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_if(!$product->is_active, 404, 'Product not found.');

        $product->load(['company', 'category', 'variants']);

        return new ProductResource($product);
    }
}
