@extends('layouts.admin')
@section('title', 'تقرير المخزون')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">تقرير المخزون</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">تقرير المخزون</h1></div>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <select name="status" class="form-control" style="width:160px">
            <option value="">كل المخزون</option>
            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>منخفض المخزون</option>
            <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>نفد المخزون</option>
            <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>متوفر</option>
        </select>
        <select name="category_id" class="form-control" style="width:180px">
            <option value="">كل التصنيفات</option>
            @foreach($categories ?? [] as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> تطبيق</button>
    </form>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px">
    @foreach([
        ['fas fa-boxes-stacked', 'إجمالي المتغيرات', $totals['total_variants'] ?? 0, 'primary'],
        ['fas fa-check', 'متوفر', $totals['in_stock'] ?? 0, 'accent'],
        ['fas fa-exclamation', 'منخفض (≤ 5)', $totals['low_stock'] ?? 0, 'warning'],
        ['fas fa-xmark', 'نفد المخزون', $totals['out_of_stock'] ?? 0, 'danger'],
    ] as [$icon, $label, $val, $color])
    <div class="stat-card {{ $color }}">
        <div class="stat-header">
            <span class="stat-label">{{ $label }}</span>
            <div class="stat-icon {{ $color }}"><i class="{{ $icon }}"></i></div>
        </div>
        <div class="stat-value">{{ number_format($val) }}</div>
    </div>
    @endforeach
</div>

{{-- Inventory table --}}
<div class="card">
    <div class="filter-bar">
        <div class="search-wrap">
            <input type="text" id="searchInput" class="form-control" placeholder="بحث بالاسم أو SKU...">
            <i class="fas fa-magnifying-glass"></i>
        </div>
        <span style="color:var(--text-3);font-size:.85rem">{{ ($variants ?? collect())->total() ?? 0 }} نتيجة</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الخيارات</th>
                    <th>SKU</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
            @forelse($variants ?? [] as $variant)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $variant->product->name_ar ?? '—' }}</div>
                    <div style="font-size:.76rem;color:var(--text-3)">{{ $variant->product->category->name_ar ?? '' }}</div>
                </td>
                <td style="font-size:.82rem">
                    @if($variant->attributeValues->count())
                    {{ $variant->attributeValues->map(fn($v) => $v->name_ar)->implode(' / ') }}
                    @else
                    <span style="color:var(--text-3)">افتراضي</span>
                    @endif
                </td>
                <td><code style="font-size:.8rem">{{ $variant->sku }}</code></td>
                <td>{{ number_format($variant->price, 2) }} ر.س</td>
                <td>
                    <span style="font-weight:700;font-size:1rem;color:{{ $variant->stock_quantity <= 0 ? 'var(--danger)' : ($variant->stock_quantity <= 5 ? 'var(--warning)' : 'var(--accent)') }}">
                        {{ $variant->stock_quantity }}
                    </span>
                </td>
                <td>
                    @if($variant->stock_quantity <= 0)
                    <span class="badge badge-danger">نفد</span>
                    @elseif($variant->stock_quantity <= 5)
                    <span class="badge badge-warning">منخفض</span>
                    @else
                    <span class="badge badge-success">متوفر</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="btn btn-xs btn-outline">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-3)">لا توجد نتائج</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($variants) && method_exists($variants, 'hasPages') && $variants->hasPages())
    <div class="pagination-wrap">
        <span>{{ $variants->total() }} سجل</span>
        {{ $variants->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('searchInput')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
