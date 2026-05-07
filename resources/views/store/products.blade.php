@extends('layouts.store')
@section('title', 'المنتجات')

@section('content')
<div class="container" style="padding-top:28px;padding-bottom:60px">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('store.home') }}">الرئيسية</a>
        <span class="sep">/</span>
        @if($currentCategory)
        <a href="{{ route('store.products') }}">المنتجات</a>
        <span class="sep">/</span>
        <span>{{ $currentCategory->name }}</span>
        @else
        <span>المنتجات</span>
        @endif
    </nav>

    <div style="display:grid;grid-template-columns:260px 1fr;gap:32px;align-items:start" id="productsLayout">

        {{-- ── Sidebar ── --}}
        <aside id="filtersSidebar" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
                <span style="font-weight:700;font-size:.95rem"><i class="fas fa-filter" style="color:var(--primary);margin-inline-end:7px"></i>تصفية النتائج</span>
                @if(request()->hasAny(['category','brand','min_price','max_price','on_sale']))
                <a href="{{ route('store.products') }}" style="font-size:.78rem;color:var(--danger)">مسح الكل</a>
                @endif
            </div>

            <form method="GET" action="{{ route('store.products') }}" id="filtersForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                {{-- Categories --}}
                <div style="padding:18px 20px;border-bottom:1px solid var(--border)">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:14px;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted)">التصنيفات</div>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.88rem">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                            الكل
                        </label>
                        @foreach($categories as $cat)
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.88rem">
                            <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                            {{ $cat->name }}
                            @if($cat->children->count())
                            <span style="font-size:.72rem;color:var(--text-muted)">({{ $cat->children->count() }})</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Brands --}}
                @if($brands->count())
                <div style="padding:18px 20px;border-bottom:1px solid var(--border)">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:14px;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted)">البراند</div>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach($brands as $brand)
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.88rem">
                            <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                                {{ in_array($brand->id, (array)request('brand', [])) ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            {{ $brand->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Price --}}
                <div style="padding:18px 20px;border-bottom:1px solid var(--border)">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:14px;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted)">نطاق السعر</div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="من" min="0"
                               style="width:80px;height:36px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 10px;font-size:.85rem;font-family:var(--font);outline:none">
                        <span style="color:var(--text-muted)">–</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى" min="0"
                               style="width:80px;height:36px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 10px;font-size:.85rem;font-family:var(--font);outline:none">
                    </div>
                    <button type="submit" style="margin-top:10px;width:100%;height:34px;border-radius:var(--radius-sm);background:var(--primary-light);color:var(--primary);font-weight:700;font-size:.82rem;border:none;cursor:pointer;font-family:var(--font)">تطبيق</button>
                </div>

                {{-- Toggles --}}
                <div style="padding:18px 20px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.88rem;margin-bottom:10px">
                        <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }} onchange="this.form.submit()">
                        عروض وتخفيضات فقط
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.88rem">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                        متوفر في المخزون
                    </label>
                </div>
            </form>
        </aside>

        {{-- ── Products Grid ── --}}
        <div>
            {{-- Sort + Count bar --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
                <div style="font-size:.9rem;color:var(--text-muted)">
                    عرض <strong style="color:var(--text)">{{ $products->count() }}</strong> من <strong style="color:var(--text)">{{ $products->total() }}</strong> منتج
                </div>
                <form method="GET" style="display:flex;align-items:center;gap:10px">
                    @foreach(request()->except('sort') as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vi)<input type="hidden" name="{{ $k }}[]" value="{{ $vi }}">@endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label style="font-size:.85rem;color:var(--text-muted)">ترتيب:</label>
                    <select name="sort" class="form-control form-select" onchange="this.form.submit()"
                            style="height:38px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 32px 0 12px;font-size:.85rem;font-family:var(--font);outline:none;cursor:pointer;background:#fff">
                        <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected' : '' }}>الأحدث</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>السعر: الأقل أولاً</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>السعر: الأعلى أولاً</option>
                        <option value="popular"    {{ request('sort') === 'popular'    ? 'selected' : '' }}>الأكثر شعبية</option>
                    </select>
                </form>
            </div>

            @if($products->count())
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:20px">
                @foreach($products as $product)
                @include('store.partials.product-card', compact('product'))
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="pagination">
                {{ $products->withQueryString()->links('pagination::simple-bootstrap-4') }}
            </div>
            @endif

            @else
            <div style="text-align:center;padding:80px 20px;color:var(--text-muted)">
                <i class="fas fa-search" style="font-size:3rem;opacity:.25;margin-bottom:16px"></i>
                <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:8px">لا توجد منتجات</h3>
                <p>جرب تغيير معايير البحث أو التصفية</p>
                <a href="{{ route('store.products') }}" class="btn btn-primary" style="margin-top:20px">عرض كل المنتجات</a>
            </div>
            @endif
        </div>

    </div>
</div>

@push('styles')
<style>
@media(max-width:768px){
    #productsLayout { grid-template-columns:1fr!important; }
    #filtersSidebar { display:none; }
}
</style>
@endpush
@endsection
