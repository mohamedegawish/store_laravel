@extends('layouts.admin')
@section('title', 'إضافة كوبون')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.coupons.index') }}">الكوبونات</a><span class="sep">/</span><span class="current">إضافة</span>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">إضافة كوبون جديد</h1></div>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
</div>
<div style="max-width:800px">
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.coupons.store') }}">
@csrf
@include('admin.coupons._form', ['coupon' => null])
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
</form>
</div></div>
</div>
@endsection
