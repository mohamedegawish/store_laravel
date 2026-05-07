<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company->id;

        $query = User::whereHas('orders', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->withCount(['orders as total_orders' => function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        }])
        ->withSum(['orders as total_spent' => function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->where('status', 'delivered');
        }], 'total_amount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort by best customers usually
        $customers = $query->orderByDesc('total_spent')->paginate(15);

        return view('company.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        $companyId = auth()->user()->company->id;

        $isCustomer = $user->orders()->where('company_id', $companyId)->exists();
        if (!$isCustomer) {
            abort(403);
        }

        $orders = $user->orders()
            ->where('company_id', $companyId)
            ->latest()
            ->get();
            
        $totalSpent = $orders->where('status', 'delivered')->sum('total_amount');
        $totalOrders = $orders->count();

        return view('company.customers.show', compact('user', 'orders', 'totalSpent', 'totalOrders'));
    }
}
