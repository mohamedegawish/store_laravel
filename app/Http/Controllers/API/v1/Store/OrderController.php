<?php

namespace App\Http\Controllers\API\V1\Store;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->with(['company', 'orderItems.product', 'orderItems.productVariant'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        Gate::authorize('view', $order);

        $order->load(['company', 'orderItems.product', 'orderItems.productVariant']);

        return new OrderResource($order);
    }

    public function cancel(Order $order): JsonResponse
    {
        Gate::authorize('view', $order); // Basic check to ensure ownership

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled.'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Order cancelled successfully.']);
    }
}
