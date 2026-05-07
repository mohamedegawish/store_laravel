
@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">My Orders</h1>
        @foreach($orders as $order)
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3>Order #{{ $order->id }} - ${{ $order->total_amount }} ({{ $order->status }})</h3>
                <a href="{{ route('front.orders.show', $order) }}" class="text-blue-500">View Details</a>
            </div>
        @endforeach
    </div>
@endsection

