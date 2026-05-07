
@extends('layouts.admin')
@section('content')
    <h1>Variants for {{ $product->name }}</h1>
    <a href="{{ route('company.products.variants.create', $product) }}" class="btn btn-primary">Add Variant</a>
    <div class="table-responsive">
        <table class="table">
            <tr><th>SKU</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->sku }}</td>
                <td>${{ $variant->price }}</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>
                    <a href="{{ route('company.products.variants.edit', [$product, $variant]) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('company.products.variants.destroy', [$product, $variant]) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection

