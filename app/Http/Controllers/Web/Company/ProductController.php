<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company->id;
        
        $query = Product::with(['category', 'variants'])
            ->where('company_id', $companyId)
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);

        return view('company.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('company.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $imagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('products', 'public');
            }
        }

        $product = Product::create([
            'company_id' => $companyId,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active'),
            'images' => $imagesPaths,
        ]);

        // Create default variant containing the stock and price
        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => Str::upper(Str::random(10)),
            'price' => $validated['price'],
            'cost' => $validated['cost'] ?? null,
            'discount' => $validated['discount'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
        ]);

        return redirect()->route('company.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح.');
    }

    public function edit(Product $product)
    {
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $categories = Category::all();
        $variant = $product->variants()->first(); // Assuming single variant for simplicity

        return view('company.products.edit', compact('product', 'categories', 'variant'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $imagesPaths = $product->images ?? [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('products', 'public');
            }
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active'),
            'images' => $imagesPaths,
        ]);

        $variant = $product->variants()->first();
        if ($variant) {
            $variant->update([
                'price' => $validated['price'],
                'cost' => $validated['cost'] ?? null,
                'discount' => $validated['discount'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
            ]);
        }

        return redirect()->route('company.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Product $product)
    {
        if ($product->company_id !== auth()->user()->company->id) {
            abort(403);
        }

        // Delete images from storage
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('company.products.index')
            ->with('success', 'تم حذف المنتج بنجاح.');
    }
}
