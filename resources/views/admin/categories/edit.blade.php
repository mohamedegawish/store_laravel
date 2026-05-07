@extends('layouts.admin')
@section('title', 'تعديل تصنيف')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.categories.index') }}">التصنيفات</a><span class="sep">/</span><span class="current">تعديل</span>
@endsection
@section('content')

<div class="page-header">
    <div><h1 class="page-title">تعديل: {{ $category->name_ar }}</h1></div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
</div>

<div style="max-width:700px">
<div class="card">
<div class="card-body">
<form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
@csrf @method('PUT')
@include('admin.categories._form', compact('category'))
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
</form>
</div>
</div>
</div>
@endsection
