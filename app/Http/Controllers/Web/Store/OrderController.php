<?php

namespace App\Http\Controllers\Web\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['company', 'orderItems.product', 'orderItems.productVariant'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        // Assume we have a view for user orders
        return view('pages.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['company', 'orderItems.product', 'orderItems.productVariant']);

        return view('pages.order_details', compact('order'));
    }
}
