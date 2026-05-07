@extends('layouts.admin')
@section('title', 'البنرات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">البنرات</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">البنرات الإعلانية</h1>
        <p class="page-subtitle">إدارة بنرات الصفحة الرئيسية والصفحات الأخرى</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة بنر</a>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>البنر</th>
                    <th>الموضع</th>
                    <th>الرابط</th>
                    <th>الفترة</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($banners as $banner)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px">
                        @if($banner->image)
                        <img src="{{ asset('storage/'.$banner->image) }}" style="width:80px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
                        @else
                        <div style="width:80px;height:40px;background:var(--surface-2);border-radius:6px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-3)"><i class="fas fa-image"></i></div>
                        @endif
                        <div>
                            <div style="font-weight:600">{{ $banner->title_ar ?? $banner->title ?? '—' }}</div>
                            <div style="font-size:.76rem;color:var(--text-3)">{{ $banner->title_en ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                    $positions = ['hero'=>'رئيسي','sidebar'=>'جانبي','popup'=>'منبثق','section'=>'قسم'];
                    @endphp
                    <span class="badge badge-info">{{ $positions[$banner->position] ?? $banner->position }}</span>
                </td>
                <td style="font-size:.8rem;color:var(--text-3)">
                    {{ $banner->url ? Str::limit($banner->url, 30) : '—' }}
                </td>
                <td style="font-size:.8rem">
                    @if($banner->starts_at || $banner->expires_at)
                    <div>{{ $banner->starts_at?->format('d/m/Y') ?? '∞' }}</div>
                    <div style="color:var(--text-3)">{{ $banner->expires_at?->format('d/m/Y') ?? '∞' }}</div>
                    @else
                    <span style="color:var(--text-3)">دائم</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $banner->is_active ? 'success' : 'secondary' }}">
                        {{ $banner->is_active ? 'نشط' : 'مخفي' }}
                    </span>
                </td>
                <td>{{ $banner->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('حذف البنر؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-3)">لا توجد بنرات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($banners->hasPages())
    <div class="pagination-wrap">
        <span>{{ $banners->total() }} بنر</span>
        {{ $banners->links() }}
    </div>
    @endif
</div>
@endsection
