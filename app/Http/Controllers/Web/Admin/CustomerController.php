<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            }))
            ->when($request->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);
        $customer->load(['orders' => fn($q) => $q->with('orderItems')->latest()->limit(10), 'addresses', 'reviews']);
        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        abort_if($customer->role !== 'customer', 403);
        $data = $request->validate([
            'notes'     => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $customer->update($data);
        return back()->with('success', 'تم تحديث بيانات العميل');
    }

    public function toggleStatus(User $customer)
    {
        abort_if($customer->role !== 'customer', 403);
        $customer->update(['is_active' => !$customer->is_active]);
        return back()->with('success', 'تم تحديث حالة العميل');
    }
}
