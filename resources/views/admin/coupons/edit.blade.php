@extends('layouts.admin')
@section('title', 'تعديل كوبون')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">الرئيسية</a><span class="sep">/</span>
    <a href="{{ route('admin.coupons.index') }}">الكوبونات</a><span class="sep">/</span><span class="current">تعديل</span>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">تعديل: {{ $coupon->code }}</h1></div>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
</div>
<div style="max-width:800px">
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
@csrf @method('PUT')
@include('admin.coupons._form', compact('coupon'))
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
</form>
</div></div>
</div>
@endsection
