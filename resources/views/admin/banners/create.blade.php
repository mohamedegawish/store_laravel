@extends('layouts.admin')
@section('title', 'إضافة بنر')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.banners.index') }}">البنرات</a><span class="sep">/</span>
    <span class="current">إضافة بنر</span>
@endsection
@section('content')

<div class="page-header">
    <h1 class="page-title">إضافة بنر جديد</h1>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة</a>
</div>

<form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-image"></i> بيانات البنر</span></div>
        <div class="card-body">
            @include('admin.banners._form')
        </div>
        <div class="card-footer" style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ البنر</button>
        </div>
    </div>
</form>
@endsection
