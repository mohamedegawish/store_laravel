
@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Order Detail #{{ $order->id }}</h1>
        <p>Total: ${{ $order->total_amount }}</p>
        <p>Status: {{ $order->status }}</p>
        <h3 class="text-xl font-bold mt-4">Items</h3>
        <ul>
            @foreach($order->orderItems as $item)
                <li>{{ $item->product?->name }} (x{{ $item->quantity }}) - ${{ $item->total_price }}</li>
            @endforeach
        </ul>
    </div>
@endsection

