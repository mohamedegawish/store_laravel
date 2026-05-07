<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Services\CartService;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function home()
    {
        $settings         = StoreSetting::current();
        $banners          = Banner::active()->orderBy('sort_order')->get();
        $categories       = Category::active()->roots()->withCount(['products' => fn($q) => $q->active()])
            ->orderBy('sort_order')->limit(12)->get();
        $featuredProducts = Product::active()->featured()->with(['activeVariants', 'brand', 'category'])
            ->inStock()->limit(12)->get();
        $trendingProducts = Product::active()->trending()->with(['activeVariants', 'brand'])
            ->inStock()->limit(8)->get();
        $bestSellers      = Product::active()->bestSeller()->with(['activeVariants', 'brand'])
            ->inStock()->limit(8)->get();
        $newArrivals      = Product::active()->with(['activeVariants', 'brand'])
            ->inStock()->latest()->limit(8)->get();

        return view('store.home', compact(
            'settings', 'banners', 'categories', 'featuredProducts',
            'trendingProducts', 'bestSellers', 'newArrivals'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::active()->with(['activeVariants', 'category', 'brand']);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name_ar', 'like', "%{$s}%")
                ->orWhere('name_en', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%"));
        }

        // Category filter
        $currentCategory = null;
        if ($request->filled('category')) {
            $currentCategory = Category::where('slug', $request->category)->first();
            if ($currentCategory) {
                $ids = $currentCategory->children()->pluck('id')->push($currentCategory->id);
                $query->whereIn('category_id', $ids);
            }
        }

        // Brand filter
        if ($request->filled('brand')) {
            $brands_filter = (array) $request->brand;
            $query->whereIn('brand_id', $brands_filter);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->whereHas('activeVariants', fn($q) => $q->where('price', '>=', (float)$request->min_price));
        }
        if ($request->filled('max_price')) {
            $query->whereHas('activeVariants', fn($q) => $q->where('price', '<=', (float)$request->max_price));
        }

        // On sale
        if ($request->boolean('on_sale')) {
            $query->whereHas('activeVariants', fn($q) => $q->whereNotNull('offer_price')
                ->where(fn($inner) => $inner->whereNull('offer_ends_at')->orWhere('offer_ends_at', '>=', now())));
        }

        // In stock
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Flags
        if ($request->boolean('featured'))    $query->featured();
        if ($request->boolean('trending'))    $query->trending();
        if ($request->boolean('best_seller')) $query->bestSeller();

        // Sort
        match($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id AND is_active = 1) ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MAX(price) FROM product_variants WHERE product_id = products.id AND is_active = 1) DESC'),
            'popular'    => $query->orderByDesc('views_count'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(24)->withQueryString();
        $categories = Category::active()->roots()->orderBy('sort_order')->get();
        $brands     = \App\Models\Brand::where('is_active', true)->orderBy('name_ar')->get();

        return view('store.products', compact('products', 'categories', 'brands', 'currentCategory'));
    }

    public function productDetail(string $slug)
    {
        $product = Product::active()
            ->with(['variants.attributeValues.attribute', 'variants.attributeValues.attributeValue', 'category', 'brand', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $product->increment('views_count');

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('variants')
            ->inStock()
            ->limit(6)
            ->get();

        $isWishlisted = false;
        if (auth()->check()) {
            $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)->exists();
        }

        $settings = StoreSetting::current();

        return view('store.product-detail', compact('product', 'relatedProducts', 'isWishlisted', 'settings'));
    }

    public function category(string $slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();
        $childIds = $category->children()->pluck('id')->push($category->id);

        $products = Product::active()
            ->with(['variants', 'category'])
            ->whereIn('category_id', $childIds)
            ->inStock()
            ->latest()
            ->paginate(24)->withQueryString();

        $subCategories = $category->activeChildren()->withCount(['products' => fn($q) => $q->active()])->get();

        return view('store.category', compact('category', 'products', 'subCategories'));
    }

    public function page(string $slug)
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        return view('store.page', compact('page'));
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        return view('store.faq', compact('faqs'));
    }

    public function contact()
    {
        return view('store.contact');
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        \App\Models\ContactMessage::create($data);

        return back()->with('success', __('store.message_sent'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $results = Product::active()
            ->with('activeVariants')
            ->where(fn($query) => $query->where('name_ar', 'like', "%{$q}%")
                ->orWhere('name_en', 'like', "%{$q}%"))
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'url'   => route('store.product', $p->slug),
                'price' => number_format($p->price ?? 0, 2) . ' ر.س',
                'image' => $p->main_image ? asset('storage/'.$p->main_image) : 'https://via.placeholder.com/40x40?text=?',
            ]);

        return response()->json($results);
    }

    public function storeReview(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'nullable|string|max:2000',
        ]);

        \App\Models\Review::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => auth()->id()],
            array_merge($data, ['is_approved' => false])
        );

        return back()->with('success', 'شكراً! سيتم مراجعة تقييمك ونشره قريباً.');
    }
}
