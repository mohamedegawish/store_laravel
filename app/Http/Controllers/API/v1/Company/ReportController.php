<?php

namespace App\Http\Controllers\API\V1\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function salesSummary(): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        $totalRevenue = Order::where('company_id', $companyId)
            ->where('status', 'delivered')
            ->sum('total_amount');

        $pendingOrders = Order::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        // Getting top selling products could be more complex, keeping it basic for now
        return response()->json([
            'status' => true,
            'data' => [
                'total_revenue' => (float) $totalRevenue,
                'pending_orders' => $pendingOrders,
            ]
        ]);
    }
}
