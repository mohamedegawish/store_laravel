@extends('layouts.admin')
@section('title', 'إدارة المنتجات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a>
    <span class="sep">/</span><span class="current">المنتجات</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">المنتجات</h1>
        <p class="page-subtitle">{{ $products->total() }} منتج</p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة منتج</a>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" class="filter-bar">
    <div class="filter-group" style="flex:2">
        <label>بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="اسم المنتج، SKU...">
    </div>
    <div class="filter-group">
        <label>التصنيف</label>
        <select name="category" class="form-control form-select">
            <option value="">الكل</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>الحالة</label>
        <select name="status" class="form-control form-select">
            <option value="">الكل</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
        </select>
    </div>
    <div class="filter-group">
        <label>المخزون</label>
        <select name="stock" class="form-control form-select">
            <option value="">الكل</option>
            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>منخفض</option>
            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>نفذ</option>
        </select>
    </div>
    <div style="display:flex;gap:8px;align-items:flex-end">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> بحث</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">مسح</a>
    </div>
</form>

{{-- Bulk Actions --}}
<form id="bulkForm" method="POST" action="{{ route('admin.products.bulk') }}">
@csrf
<div class="card">
    <div class="card-header" style="padding:12px 16px">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.85rem;font-weight:600">
                <input type="checkbox" id="selectAll" style="width:16px;height:16px"> تحديد الكل
            </label>
            <select name="action" class="form-control form-select" style="width:auto;min-width:160px" id="bulkAction">
                <option value="">-- إجراء جماعي --</option>
                <option value="activate">تفعيل</option>
                <option value="deactivate">إلغاء تفعيل</option>
                <option value="feature">تمييز</option>
                <option value="unfeature">إلغاء التمييز</option>
                <option value="delete">حذف</option>
            </select>
            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('تأكيد تنفيذ الإجراء؟')">تطبيق</button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr>
                <th style="width:40px"><input type="checkbox" id="selectAllHead" style="width:16px;height:16px"></th>
                <th>المنتج</th>
                <th>التصنيف</th>
                <th>السعر</th>
                <th>المخزون</th>
                <th>الحالة</th>
                <th>مميز</th>
                <th>إجراءات</th>
            </tr></thead>
            <tbody>
            @forelse($products as $product)
            @php $variant = $product->defaultVariant; @endphp
            <tr>
                <td><input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="row-check" style="width:16px;height:16px"></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <img src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://via.placeholder.com/44' }}"
                             style="width:44px;height:44px;border-radius:8px;object-fit:cover;flex-shrink:0" alt="">
                        <div>
                            <div style="font-weight:600;font-size:.88rem">{{ $product->name_ar }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted)">{{ $product->name_en }}</div>
                            @if($product->sku)<div style="font-size:.72rem;color:var(--text-muted)">SKU: {{ $product->sku }}</div>@endif
                        </div>
                    </div>
                </td>
                <td style="font-size:.85rem">{{ $product->category?->name_ar ?? '-' }}</td>
                <td style="font-weight:700;font-size:.9rem">
                    @if($variant)
                        {{ number_format($variant->effective_price, 2) }} ر.س
                        @if($variant->hasActiveOffer())
                        <div style="font-size:.72rem;color:var(--danger);text-decoration:line-through">{{ number_format($variant->price, 2) }}</div>
                        @endif
                    @else —
                    @endif
                </td>
                <td>
                    @if($variant)
                        @if($variant->isOutOfStock())
                            <span class="badge badge-danger">نفذ</span>
                        @elseif($variant->isLowStock())
                            <span class="badge badge-warning">{{ $variant->stock_quantity }} — منخفض</span>
                        @else
                            <span class="badge badge-success">{{ $variant->stock_quantity }}</span>
                        @endif
                    @else
                        <span style="color:var(--text-muted)">—</span>
                    @endif
                </td>
                <td>
                    @php $sc = ['active'=>'badge-success','inactive'=>'badge-danger','draft'=>'badge-secondary'] @endphp
                    @php $sl = ['active'=>'نشط','inactive'=>'غير نشط','draft'=>'مسودة'] @endphp
                    <span class="badge {{ $sc[$product->status] ?? 'badge-secondary' }}">{{ $sl[$product->status] ?? $product->status }}</span>
                </td>
                <td style="text-align:center">
                    @if($product->is_featured) <i class="fas fa-star" style="color:#F59E0B"></i> @else <i class="far fa-star" style="color:var(--border)"></i> @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-xs btn-outline" title="تعديل"><i class="fas fa-pencil"></i></a>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-xs btn-outline" title="عرض"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline" title="{{ $product->status === 'active' ? 'إلغاء تفعيل' : 'تفعيل' }}">
                                <i class="fas fa-{{ $product->status === 'active' ? 'toggle-on' : 'toggle-off' }}" style="color:{{ $product->status === 'active' ? 'var(--accent)' : 'var(--text-muted)' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline" onsubmit="return confirm('حذف هذا المنتج؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                <i class="fas fa-box" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3"></i>
                لا توجد منتجات. <a href="{{ route('admin.products.create') }}" style="color:var(--primary)">إضافة أول منتج</a>
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        <span>إجمالي: {{ $products->total() }} منتج</span>
        {{ $products->links() }}
    </div>
</div>
</form>

@push('scripts')
<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});
document.getElementById('selectAllHead')?.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    document.getElementById('selectAll').checked = this.checked;
});
</script>
@endpush
@endsection
