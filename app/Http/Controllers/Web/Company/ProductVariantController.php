<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        Gate::authorize('view', $product);
        $variants = $product->variants()->latest()->paginate(20);
        return view('company.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        Gate::authorize('update', $product);
        return view('company.variants.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:product_variants',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->variants()->create($validated);

        return redirect()->route('company.products.variants.index', $product)->with('success', 'Variant added.');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        Gate::authorize('update', $product);
        $this->ensureBelongsToProduct($product, $variant);

        return view('company.variants.edit', compact('product', 'variant'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        Gate::authorize('update', $product);
        $this->ensureBelongsToProduct($product, $variant);

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_variants')->ignore($variant->id)],
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $variant->update($validated);

        return redirect()->route('company.products.variants.index', $product)->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        Gate::authorize('update', $product);
        $this->ensureBelongsToProduct($product, $variant);

        $variant->delete();

        return redirect()->back()->with('success', 'Variant deleted.');
    }

    private function ensureBelongsToProduct($product, $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);
    }
}
