
@extends('layouts.admin')
@section('content')
    <h1>Edit Variant</h1>
    <form action="{{ route('company.products.variants.update', [$product, $variant]) }}" method="POST">
        @csrf @method('PUT')
        <input class="form-control" type="text" name="sku" value="{{ $variant->sku }}" required>
        <input class="form-control" type="number" step="0.01" name="price" value="{{ $variant->price }}" required>
        <input class="form-control" type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" required>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>
@endsection

