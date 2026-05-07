@extends('layouts.admin')
@section('title', 'إضافة صفحة')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.pages.index') }}">الصفحات</a><span class="sep">/</span>
    <span class="current">إضافة صفحة</span>
@endsection
@section('content')

<div class="page-header">
    <h1 class="page-title">إضافة صفحة جديدة</h1>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة</a>
</div>

<form method="POST" action="{{ route('admin.pages.store') }}">
    @csrf
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-file-lines"></i> محتوى الصفحة</span></div>
        <div class="card-body">
            @include('admin.pages._form')
        </div>
        <div class="card-footer" style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الصفحة</button>
        </div>
    </div>
</form>
@endsection
