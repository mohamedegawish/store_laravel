<?php

namespace App\Http\Controllers\API\V1\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        return response()->json([
            'status' => true,
            'data' => [
                'total_products' => Product::where('company_id', $companyId)->count(),
                'total_orders' => Order::where('company_id', $companyId)->count(),
                'total_revenue' => Order::where('company_id', $companyId)
                                        ->where('status', 'delivered')
                                        ->sum('total_amount'),
                'recent_orders' => Order::where('company_id', $companyId)
                                        ->with('user')
                                        ->latest()
                                        ->take(5)
                                        ->get(),
            ]
        ]);
    }
}
