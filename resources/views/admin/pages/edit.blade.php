@extends('layouts.admin')
@section('title', 'تعديل صفحة')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.pages.index') }}">الصفحات</a><span class="sep">/</span>
    <span class="current">تعديل: {{ $page->title_ar ?? $page->title }}</span>
@endsection
@section('content')

<div class="page-header">
    <h1 class="page-title">تعديل الصفحة</h1>
    <div style="display:flex;gap:8px">
        <a href="{{ route('store.page', $page->slug) }}" class="btn btn-secondary" target="_blank"><i class="fas fa-eye"></i> معاينة</a>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة</a>
    </div>
</div>

<form method="POST" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-file-lines"></i> محتوى الصفحة</span></div>
        <div class="card-body">
            @include('admin.pages._form')
        </div>
        <div class="card-footer" style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
        </div>
    </div>
</form>
@endsection
