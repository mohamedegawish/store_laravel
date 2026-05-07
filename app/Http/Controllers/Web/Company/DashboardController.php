<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;

        if (! $company) {
            abort(403, 'لا توجد شركة مرتبطة بهذا الحساب.');
        }

        $companyId = $company->id;

        // ── Core Stats ──────────────────────────────────────────────
        $totalProducts  = $company->products()->count();
        $totalOrders    = $company->orders()->count();
        $pendingOrders  = $company->orders()->where('status', 'pending')->count();
        $completedOrders = $company->orders()->where('status', 'delivered')->count();
        $cancelledOrders = $company->orders()->where('status', 'cancelled')->count();

        // Revenue (paid / delivered)
        $monthlyRevenue = $company->orders()
            ->where('status', 'delivered')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $totalRevenue = $company->orders()
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Profit (only if cost exists on variants)
        $totalProfit = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'oi.product_variant_id', '=', 'pv.id')
            ->where('o.company_id', $companyId)
            ->where('o.status', 'delivered')
            ->whereNotNull('pv.cost')
            ->selectRaw('SUM((oi.unit_price - pv.cost) * oi.quantity) as profit')
            ->value('profit') ?? 0;

        // ── Monthly Sales Chart (12 months) ─────────────────────────
        $salesByMonth = $company->orders()
            ->where('status', 'delivered')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlySales = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales[] = round($salesByMonth[$i] ?? 0, 2);
        }

        // ── Orders by Status (doughnut) ──────────────────────────────
        $ordersByStatus = $company->orders()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Smart Insights ───────────────────────────────────────────
        $bestProduct = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('o.company_id', $companyId)
            ->selectRaw('p.id, p.name, p.images, SUM(oi.quantity) as total_sold')
            ->groupBy('p.id', 'p.name', 'p.images')
            ->orderByDesc('total_sold')
            ->first();

        $worstProduct = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('o.company_id', $companyId)
            ->selectRaw('p.id, p.name, p.images, SUM(oi.quantity) as total_sold')
            ->groupBy('p.id', 'p.name', 'p.images')
            ->orderBy('total_sold')
            ->first();

        // Growth rate (compare this month vs last month)
        $thisMonthRevenue = $monthlySales[now()->month - 1] ?? 0;
        $lastMonthRevenue = $monthlySales[max(0, now()->month - 2)] ?? 0;
        $growthRate = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Average order value
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // ── Low Stock Alerts ─────────────────────────────────────────
        $lowStockProducts = $company->products()
            ->whereHas('variants', fn ($q) => $q->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0))
            ->with(['variants' => fn ($q) => $q->where('stock_quantity', '<=', 5)])
            ->take(5)
            ->get();

        $outOfStockCount = $company->products()
            ->whereHas('variants', fn ($q) => $q->where('stock_quantity', 0))
            ->count();

        // ── Recent Data ──────────────────────────────────────────────
        $recentOrders   = $company->orders()->with('user')->latest()->take(7)->get();
        $recentProducts = $company->products()->with('variants')->latest()->take(5)->get();

        return view('company.dashboard', compact(
            'company',
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'monthlyRevenue',
            'totalRevenue',
            'totalProfit',
            'monthlySales',
            'ordersByStatus',
            'bestProduct',
            'worstProduct',
            'growthRate',
            'avgOrderValue',
            'lowStockProducts',
            'outOfStockCount',
            'recentOrders',
            'recentProducts',
        ));
    }
}
