@extends('layouts.admin')
@section('title', 'الصفحات')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span><span class="current">الصفحات</span>
@endsection
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">إدارة الصفحات</h1>
        <p class="page-subtitle">صفحات المتجر مثل "من نحن" و"سياسة الخصوصية"</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة صفحة</a>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>الصفحة</th>
                    <th>الرابط</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pages as $page)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $page->title_ar ?? $page->title }}</div>
                    <div style="font-size:.76rem;color:var(--text-3)">{{ $page->title_en }}</div>
                </td>
                <td><code style="font-size:.8rem;background:var(--surface-2);padding:2px 8px;border-radius:4px">/page/{{ $page->slug }}</code></td>
                <td>
                    <span class="badge badge-{{ $page->is_published ? 'success' : 'secondary' }}">
                        {{ $page->is_published ? 'منشورة' : 'مسودة' }}
                    </span>
                </td>
                <td>{{ $page->sort_order }}</td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('store.page', $page->slug) }}" class="btn btn-xs btn-outline" target="_blank" title="معاينة"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-xs btn-outline"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('حذف الصفحة؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline" style="color:var(--danger)"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-3)">لا توجد صفحات</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
