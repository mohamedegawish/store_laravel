@extends('layouts.company')

@section('title', 'قائمة المنتجات | إدارة المتجر')

@section('breadcrumb')
    <a href="{{ route('company.dashboard') }}">لوحة التحكم</a>
    <span class="sep"><i class="fas fa-chevron-left"></i></span>
    <span class="current">المنتجات</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">المنتجات</h1>
        <p class="page-subtitle">إدارة كتالوج المنتجات، الأسعار، والمخزون.</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('company.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة منتج جديد
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-direction: column; align-items: stretch; gap: 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="card-title">قائمة المنتجات</h3>
            
            {{-- View Toggle --}}
            <div style="display:flex; background:var(--surface-2); padding:4px; border-radius:var(--radius-md);">
                <button type="button" onclick="setView('table')" id="btnViewTable" class="icon-btn active" style="width:32px;height:32px;border:none;background:var(--surface);box-shadow:var(--shadow-sm);"><i class="fas fa-list"></i></button>
                <button type="button" onclick="setView('grid')" id="btnViewGrid" class="icon-btn" style="width:32px;height:32px;border:none;background:transparent;"><i class="fas fa-grid-2"></i></button>
            </div>
        </div>
        
        <div class="filter-bar">
            <form action="{{ route('company.products.index') }}" method="GET" class="search-wrap" style="flex:1;">
                <input type="text" name="search" class="form-control" placeholder="ابحث باسم المنتج..." value="{{ request('search') }}">
                <i class="fas fa-search search-icon"></i>
            </form>
            @if(request('search'))
                <a href="{{ route('company.products.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> مسح البحث</a>
            @endif
        </div>
    </div>

    {{-- TABLE VIEW --}}
    <div class="table-responsive" id="tableView">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px;">الصورة</th>
                    <th>اسم المنتج</th>
                    <th>القسم</th>
                    <th>السعر</th>
                    <th>المخزون</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php $variant = $product->variants->first(); @endphp
                    <tr>
                        <td>
                            <div style="width:40px;height:40px;border-radius:8px;background:var(--surface-2);overflow:hidden;display:flex;align-items:center;justify-content:center;color:var(--text-3);">
                                @if($product->images && count($product->images))
                                    <img src="{{ asset('storage/'.$product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="fas fa-image"></i>
                                @endif
                            </div>
                        </td>
                        <td style="font-weight:600;">{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td style="font-weight:700; color:var(--primary);">
                            @if($variant)
                                {{ number_format($variant->price - ($variant->discount ?? 0), 2) }} ج.م
                                @if($variant->discount > 0)
                                    <div style="font-size:0.75rem; text-decoration:line-through; color:var(--text-light); font-weight:normal;">{{ number_format($variant->price, 2) }}</div>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($variant)
                                @if($variant->stock_quantity == 0)
                                    <span class="badge badge-danger">نفذت الكمية</span>
                                @elseif($variant->stock_quantity <= 5)
                                    <span class="badge badge-warning">{{ $variant->stock_quantity }} متبقي</span>
                                @else
                                    <span class="badge badge-success">{{ $variant->stock_quantity }} متوفر</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" style="gap:5px;">
                                <a href="{{ route('company.products.edit', $product) }}" class="btn btn-outline-primary btn-sm" title="تعديل"><i class="fas fa-pen"></i></a>
                                <button type="button" class="btn btn-outline-primary btn-sm" style="color:var(--danger); border-color:var(--danger);" onclick="confirmDelete('{{ route('company.products.destroy', $product) }}', '{{ $product->name }}')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h3>لا توجد منتجات</h3>
                                <p>لم يتم العثور على منتجات تطابق بحثك، أو لم تقم بإضافة منتجات بعد.</p>
                                <a href="{{ route('company.products.create') }}" class="btn btn-primary" style="margin-top:15px;"><i class="fas fa-plus"></i> إضافة منتج</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- GRID VIEW --}}
    <div id="gridView" style="display:none; padding:20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            @foreach($products as $product)
                @php $variant = $product->variants->first(); @endphp
                <div style="border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; background:var(--surface);">
                    <div style="height:160px; background:var(--surface-2); position:relative; display:flex; align-items:center; justify-content:center; color:var(--text-3);">
                        @if($product->images && count($product->images))
                            <img src="{{ asset('storage/'.$product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="fas fa-image" style="font-size:2.5rem;"></i>
                        @endif
                        <div style="position:absolute; top:10px; right:10px; display:flex; gap:5px;">
                            <span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }} shadow-sm">
                                {{ $product->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </div>
                    </div>
                    <div style="padding:15px; display:flex; flex-direction:column; gap:8px;">
                        <div style="font-size:0.75rem; color:var(--text-light);">{{ $product->category?->name ?? 'عام' }}</div>
                        <h4 style="font-weight:700; font-size:0.95rem; line-height:1.4;">{{ Str::limit($product->name, 40) }}</h4>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                            <div style="font-weight:800; color:var(--primary); font-size:1.1rem;">
                                {{ $variant ? number_format($variant->price - ($variant->discount ?? 0), 2) . ' ج.م' : '—' }}
                            </div>
                            <span style="font-size:0.8rem; color:{{ $variant && $variant->stock_quantity > 5 ? 'var(--success)' : ($variant && $variant->stock_quantity > 0 ? 'var(--warning)' : 'var(--danger)') }}; font-weight:600;">
                                {{ $variant ? $variant->stock_quantity . ' متبقي' : '—' }}
                            </span>
                        </div>
                        
                        <div style="display:flex; gap:8px; margin-top:12px;">
                            <a href="{{ route('company.products.edit', $product) }}" class="btn btn-secondary btn-sm" style="flex:1; justify-content:center;">تعديل</a>
                            <button type="button" class="btn btn-outline-primary btn-sm" style="color:var(--danger); border-color:var(--danger);" onclick="confirmDelete('{{ route('company.products.destroy', $product) }}', '{{ $product->name }}')"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($products->hasPages())
        <div class="card-footer" id="paginationFooter">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function setView(view) {
        const table = document.getElementById('tableView');
        const grid  = document.getElementById('gridView');
        const btnT  = document.getElementById('btnViewTable');
        const btnG  = document.getElementById('btnViewGrid');

        if(view === 'grid') {
            table.style.display = 'none';
            grid.style.display = 'block';
            btnT.style.background = 'transparent';
            btnT.style.boxShadow = 'none';
            btnT.classList.remove('active');
            btnG.style.background = 'var(--surface)';
            btnG.style.boxShadow = 'var(--shadow-sm)';
            btnG.classList.add('active');
        } else {
            table.style.display = 'block';
            grid.style.display = 'none';
            btnG.style.background = 'transparent';
            btnG.style.boxShadow = 'none';
            btnG.classList.remove('active');
            btnT.style.background = 'var(--surface)';
            btnT.style.boxShadow = 'var(--shadow-sm)';
            btnT.classList.add('active');
        }
        localStorage.setItem('productsView', view);
    }

    // Restore view
    if(localStorage.getItem('productsView') === 'grid') {
        setView('grid');
    }
</script>
@endpush
