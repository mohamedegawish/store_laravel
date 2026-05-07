<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['company', 'category', 'variants'])
            ->where('is_active', true)
            ->when($request->query('category_id'), fn($q, $id) => $q->where('category_id', $id))
            ->latest()
            ->paginate(12);

        return view('pages.home', compact('products'));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_active, 404, 'Product not found.');

        $product->load(['company', 'category', 'variants']);

        return view('pages.product_details', compact('product'));
    }
}
