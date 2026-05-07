<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', "%{$request->search}%")
                  ->orWhere('name_en', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->brand, fn($q) => $q->where('brand_id', $request->brand))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->stock === 'low', fn($q) => $q->lowStock())
            ->when($request->stock === 'out', fn($q) => $q->whereHas('variants', fn($q) => $q->where('stock_quantity', '<=', 0)))
            ->latest();

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::active()->roots()->orderBy('name_ar')->get();
        $brands     = Brand::where('is_active', true)->orderBy('name_ar')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $brands     = Brand::where('is_active', true)->orderBy('name_ar')->get();
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    public function store(ProductRequest $request)
    {
        $data  = $request->validated();
        $files = $request->only(['images']);

        $this->productService->create($data, $files);

        return redirect()->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'variants.attributeValues.attribute', 'variants.attributeValues.attributeValue', 'reviews.user', 'orderItems']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'category', 'brand']);
        $categories = Category::active()->orderBy('sort_order')->get();
        $brands     = Brand::where('is_active', true)->orderBy('name_ar')->get();
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data  = $request->validated();
        $files = $request->only(['images']);

        $this->productService->update($product, $data, $files);

        return redirect()->route('admin.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);
        return back()->with('success', 'تم حذف المنتج بنجاح');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'تم تحديث حالة المنتج');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'     => 'required|in:delete,activate,deactivate,feature,unfeature',
            'product_ids'=> 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->product_ids);

        match ($request->action) {
            'delete'     => $products->each(fn($p) => $this->productService->delete($p)),
            'activate'   => $products->update(['status' => 'active']),
            'deactivate' => $products->update(['status' => 'inactive']),
            'feature'    => $products->update(['is_featured' => true]),
            'unfeature'  => $products->update(['is_featured' => false]),
        };

        return back()->with('success', 'تم تنفيذ العملية بنجاح');
    }

    // ── Variant management ─────────────────────────────────────────────
    public function storeVariant(Request $request, Product $product)
    {
        $data = $request->validate([
            'name_ar'        => 'nullable|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'sku'            => 'nullable|string|max:100',
            'price'          => 'required|numeric|min:0',
            'cost'           => 'nullable|numeric|min:0',
            'offer_price'    => 'nullable|numeric|min:0',
            'offer_starts_at'=> 'nullable|date',
            'offer_ends_at'  => 'nullable|date',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert'=> 'nullable|integer|min:0',
            'is_active'      => 'boolean',
            'image'          => 'nullable|image|max:2048',
        ]);

        $this->productService->createVariant($product, $data, $request->file('image'));

        return back()->with('success', 'تم إضافة المتغير بنجاح');
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        $data = $request->validate([
            'name_ar'        => 'nullable|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'price'          => 'required|numeric|min:0',
            'cost'           => 'nullable|numeric|min:0',
            'offer_price'    => 'nullable|numeric|min:0',
            'offer_starts_at'=> 'nullable|date',
            'offer_ends_at'  => 'nullable|date',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert'=> 'nullable|integer|min:0',
            'is_active'      => 'boolean',
            'image'          => 'nullable|image|max:2048',
        ]);

        $this->productService->updateVariant($variant, $data, $request->file('image'));

        return back()->with('success', 'تم تحديث المتغير بنجاح');
    }

    public function destroyVariant(Product $product, ProductVariant $variant)
    {
        $this->productService->deleteVariant($variant);
        return back()->with('success', 'تم حذف المتغير بنجاح');
    }
}
