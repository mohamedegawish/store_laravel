@extends('layouts.admin')
@section('title', 'البراندات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">البراندات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">البراندات</h1><p class="page-subtitle">{{ $brands->count() }} براند</p></div>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة براند</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>البراند</th><th>الرابط</th><th>المنتجات</th><th>الحالة</th><th>الترتيب</th><th>إجراءات</th></tr></thead>
            <tbody>
            @forelse($brands as $brand)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        @if($brand->logo)
                        <img src="{{ asset('storage/'.$brand->logo) }}" style="width:36px;height:36px;border-radius:6px;object-fit:contain;background:#f8f8f8;padding:2px">
                        @else
                        <div style="width:36px;height:36px;border-radius:6px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--text-muted)"><i class="fas fa-tag"></i></div>
                        @endif
                        <div>
                            <div style="font-weight:600">{{ $brand->name_ar }}</div>
                            <div style="font-size:.76rem;color:var(--text-muted)">{{ $brand->name_en }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.8rem;color:var(--text-muted)">{{ $brand->slug }}</td>
                <td>{{ $brand->products_count ?? 0 }}</td>
                <td><span class="badge badge-{{ $brand->is_active ? 'success' : 'secondary' }}">{{ $brand->is_active ? 'نشط' : 'مخفي' }}</span></td>
                <td>{{ $brand->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('حذف البراند؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">لا توجد براندات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
