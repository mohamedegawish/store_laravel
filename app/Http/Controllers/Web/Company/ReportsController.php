<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function sales(Request $request)
    {
        $company   = auth()->user()->company;
        $companyId = $company->id;

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        // Monthly grouping for the year
        $monthlySales = $company->orders()
            ->where('status', 'delivered')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total, COUNT(*) as orders_count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month'        => $i,
                'total'        => round($monthlySales[$i]->total ?? 0, 2),
                'orders_count' => $monthlySales[$i]->orders_count ?? 0,
            ];
        }

        // In range stats
        $totalRevenue = $company->orders()
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');

        $totalOrders = $company->orders()
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        $totalProfit = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'oi.product_variant_id', '=', 'pv.id')
            ->where('o.company_id', $companyId)
            ->where('o.status', 'delivered')
            ->whereNotNull('pv.cost')
            ->whereBetween('o.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->selectRaw('SUM((oi.unit_price - pv.cost) * oi.quantity) as profit')
            ->value('profit') ?? 0;

        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Orders for the table
        $orders = $company->orders()
            ->with('user')
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        return view('company.reports.sales', compact(
            'monthlyData',
            'totalRevenue',
            'totalOrders',
            'totalProfit',
            'avgOrderValue',
            'orders',
            'from',
            'to',
        ));
    }

    public function products(Request $request)
    {
        $company   = auth()->user()->company;
        $companyId = $company->id;

        $topProducts = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('product_variants as pv', 'oi.product_variant_id', '=', 'pv.id')
            ->where('o.company_id', $companyId)
            ->selectRaw('
                p.id, p.name, p.images,
                SUM(oi.quantity) as total_sold,
                SUM(oi.unit_price * oi.quantity) as total_revenue,
                SUM(CASE WHEN pv.cost IS NOT NULL THEN (oi.unit_price - pv.cost)*oi.quantity ELSE 0 END) as total_profit
            ')
            ->groupBy('p.id', 'p.name', 'p.images')
            ->orderByDesc('total_sold')
            ->paginate(20);

        return view('company.reports.products', compact('topProducts'));
    }
}
