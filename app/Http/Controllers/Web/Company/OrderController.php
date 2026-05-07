<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company->id;
        
        $query = Order::with(['user'])->where('company_id', $companyId)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15);

        return view('company.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->company_id !== auth()->user()->company->id) abort(403);

        $order->load(['user', 'orderItems.product', 'orderItems.productVariant']);

        return view('company.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->company_id !== auth()->user()->company->id) abort(403);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}
