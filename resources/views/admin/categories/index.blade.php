@extends('layouts.admin')
@section('title', 'التصنيفات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">التصنيفات</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">التصنيفات</h1></div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة تصنيف</a>
</div>

@if(session('success'))
<div class="alert alert-success" style="background:#D1FAE5;border:1px solid #A7F3D0;border-radius:var(--radius);padding:12px 18px;margin-bottom:16px;color:#065F46">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>التصنيف</th><th>الرابط</th><th>الأب</th><th>الحالة</th><th>الترتيب</th><th>إجراءات</th></tr></thead>
            <tbody>
            @forelse($query as $cat)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        @if($cat->image)
                        <img src="{{ asset('storage/'.$cat->image) }}" style="width:36px;height:36px;border-radius:6px;object-fit:cover">
                        @else
                        <div style="width:36px;height:36px;border-radius:6px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--text-muted)"><i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i></div>
                        @endif
                        <div>
                            <div style="font-weight:600">{{ $cat->name_ar }}</div>
                            <div style="font-size:.76rem;color:var(--text-muted)">{{ $cat->name_en }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.8rem;color:var(--text-muted)">{{ $cat->slug }}</td>
                <td style="font-size:.85rem">{{ $cat->parent?->name_ar ?? '—' }}</td>
                <td><span class="badge badge-{{ $cat->is_active ? 'success' : 'secondary' }}">{{ $cat->is_active ? 'نشط' : 'مخفي' }}</span></td>
                <td>{{ $cat->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('حذف التصنيف؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">لا توجد تصنيفات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
