<?php

namespace App\Http\Controllers\API\V1\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'total_users' => User::count(),
                'total_companies' => Company::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            ]
        ]);
    }
}
