
@extends('layouts.admin')
@section('content')
    <h1>Create Variant</h1>
    <form action="{{ route('company.products.variants.store', $product) }}" method="POST">
        @csrf
        <input class="form-control" type="text" name="sku" placeholder="SKU" required>
        <input class="form-control" type="number" step="0.01" name="price" placeholder="Price" required>
        <input class="form-control" type="number" name="stock_quantity" placeholder="Stock" required>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
@endsection

