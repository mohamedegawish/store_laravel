<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    /**
     * Marketplace generic home (lists all active vendors).
     */
    public function index()
    {
        $companies = Company::where('status', 'active')->with('storeSetting')->get();
        // Return a marketplace landing page view
        return view('storefront.index', compact('companies'));
    }

    /**
     * Resolve company by slug, ensuring it's active.
     */
    private function resolveCompany(string $slug): Company
    {
        return Company::where('slug', $slug)
            ->where('status', 'active')
            ->with('storeSetting')
            ->firstOrFail();
    }

    public function home(string $slug)
    {
        $company = $this->resolveCompany($slug);

        $categories = Category::withCount([
            'products' => fn ($q) => $q->where('company_id', $company->id)->where('is_active', true),
        ])->having('products_count', '>', 0)->get();

        $featuredProducts = Product::where('company_id', $company->id)
            ->where('is_active', true)
            ->with('variants', 'category')
            ->latest()
            ->take(8)
            ->get();

        // Products with discounts (for offers section)
        $discountedProducts = Product::where('company_id', $company->id)
            ->where('is_active', true)
            ->whereHas('variants', fn ($q) => $q->where('discount', '>', 0))
            ->with('variants', 'category')
            ->latest()
            ->take(4)
            ->get();

        return view('storefront.home', compact('company', 'categories', 'featuredProducts', 'discountedProducts'));
    }

    public function products(string $slug, Request $request)
    {
        $company = $this->resolveCompany($slug);

        $categories = Category::withCount([
            'products' => fn ($q) => $q->where('company_id', $company->id)->where('is_active', true),
        ])->having('products_count', '>', 0)->get();

        $query = Product::where('company_id', $company->id)
            ->where('is_active', true)
            ->with('variants', 'category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Sort
        match ($request->input('sort', 'newest')) {
            'price_asc'  => $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                                  ->orderBy('product_variants.price')
                                  ->select('products.*'),
            'price_desc' => $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                                  ->orderByDesc('product_variants.price')
                                  ->select('products.*'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.products', compact('company', 'categories', 'products'));
    }

    public function productDetails(string $slug, Product $product)
    {
        $company = $this->resolveCompany($slug);

        if ($product->company_id !== $company->id || ! $product->is_active) {
            abort(404);
        }

        $product->load('variants', 'category');

        $relatedProducts = Product::where('company_id', $company->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('variants', 'category')
            ->take(4)
            ->get();

        return view('storefront.product_details', compact('company', 'product', 'relatedProducts'));
    }

    public function cart(string $slug)
    {
        $company   = $this->resolveCompany($slug);
        $cart      = auth()->user()->cart;
        $cartItems = $cart
            ? $cart->cartItems()
                   ->with(['product.variants', 'productVariant'])
                   ->get()
                   ->filter(fn ($i) => $i->product?->company_id === $company->id)
            : collect([]);

        $total = $cartItems->sum(fn ($i) => $i->quantity * (($i->productVariant->price ?? 0) - ($i->productVariant->discount ?? 0)));

        return view('storefront.cart', compact('company', 'cartItems', 'total'));
    }

    public function checkout(string $slug)
    {
        $company   = $this->resolveCompany($slug);
        $cart      = auth()->user()->cart;
        $cartItems = $cart
            ? $cart->cartItems()
                   ->with(['product', 'productVariant'])
                   ->get()
                   ->filter(fn ($i) => $i->product?->company_id === $company->id)
            : collect([]);

        if ($cartItems->isEmpty()) {
            return redirect()->route('front.cart', $slug)->with('error', 'السلة فارغة، أضف منتجات أولاً.');
        }

        $total = $cartItems->sum(fn ($i) => $i->quantity * (($i->productVariant->price ?? 0) - ($i->productVariant->discount ?? 0)));

        return view('storefront.checkout', compact('company', 'cartItems', 'total'));
    }

    public function processCheckout(Request $request, string $slug)
    {
        $company = $this->resolveCompany($slug);

        $validated = $request->validate([
            'address' => 'required|string|max:500',
            'city'    => 'required|string|max:100',
            'phone'   => 'required|string|max:30',
            'notes'   => 'nullable|string|max:500',
        ]);

        $cart      = auth()->user()->cart;
        $cartItems = $cart
            ? $cart->cartItems()
                   ->with(['productVariant'])
                   ->get()
                   ->filter(fn ($i) => $i->product?->company_id === $company->id)
            : collect([]);

        if ($cartItems->isEmpty()) {
            return redirect()->route('front.cart', $slug)->with('error', 'السلة فارغة.');
        }

        $total = $cartItems->sum(fn ($i) => $i->quantity * (($i->productVariant->price ?? 0) - ($i->productVariant->discount ?? 0)));

        $order = Order::create([
            'company_id'      => $company->id,
            'user_id'         => auth()->id(),
            'status'          => 'pending',
            'payment_status'  => 'pending',
            'total_amount'    => $total,
            'shipping_address' => [
                'address' => $validated['address'],
                'city'    => $validated['city'],
                'phone'   => $validated['phone'],
            ],
            'notes' => $validated['notes'],
        ]);

        foreach ($cartItems as $item) {
            $unitPrice = ($item->productVariant->price ?? 0) - ($item->productVariant->discount ?? 0);

            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity'           => $item->quantity,
                'unit_price'         => $unitPrice,
            ]);

            if (($item->productVariant->stock_quantity ?? 0) >= $item->quantity) {
                $item->productVariant->decrement('stock_quantity', $item->quantity);
            }

            $item->delete();
        }

        return redirect()->route('front.home', $slug)
            ->with('success', "✅ تم استلام طلبك بنجاح! رقم طلبك هو: #{$order->id}");
    }
}
