<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function sales(Request $request)
    {
        $from   = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to     = $request->get('to', now()->format('Y-m-d'));
        $period = $request->get('period', 'day');

        $orders = Order::with('user')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->whereNotIn('status', ['cancelled'])
            ->orderByDesc('created_at')
            ->paginate(30)->withQueryString();

        $totals = [
            'orders'   => $orders->total(),
            'revenue'  => Order::whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount'),
            'avg_order'=> Order::whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->whereNotIn('status', ['cancelled'])
                ->avg('total_amount') ?? 0,
        ];

        $chart   = $this->reports->getSalesChart('month');
        $byStatus= $this->reports->getOrdersByStatus();

        return view('admin.reports.sales', compact('orders', 'totals', 'chart', 'byStatus', 'from', 'to'));
    }

    public function products(Request $request)
    {
        $from        = $request->get('from');
        $to          = $request->get('to');
        $topProducts = $this->reports->getTopProducts(20, $from, $to);
        $lowStockItems = $this->reports->getLowStockReport();
        $byCategory  = $this->reports->getRevenueByCategory();
        $stats       = $this->reports->getDashboardStats();

        $totals = [
            'total_products' => $stats['total_products'],
            'active_products'=> $stats['total_products'],
            'low_stock'      => $stats['low_stock_variants'],
            'out_of_stock'   => $stats['out_of_stock_variants'],
        ];

        return view('admin.reports.products', compact('topProducts', 'lowStockItems', 'byCategory', 'totals', 'from', 'to'));
    }

    public function customers(Request $request)
    {
        $topCustomers = $this->reports->getTopCustomers(20);
        $totalCustomers = User::where('role', 'customer')->count();
        $newCustomers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $returningCustomers = User::where('role', 'customer')
            ->has('orders', '>=', 2)
            ->count();
        $avgLtv = User::where('role', 'customer')
            ->withSum('orders', 'total_amount')
            ->get()
            ->avg('orders_sum_total_amount') ?? 0;

        $totals = [
            'total_customers'    => $totalCustomers,
            'new_customers'      => $newCustomers,
            'returning_customers'=> $returningCustomers,
            'avg_ltv'            => $avgLtv,
        ];
        $chart = $this->reports->getSalesChart('month');

        return view('admin.reports.customers', compact('topCustomers', 'totals', 'chart'));
    }

    public function inventory(Request $request)
    {
        $query = \App\Models\ProductVariant::with(['product.category', 'attributeValues'])
            ->where('is_active', true);

        if ($request->status === 'out') $query->where('stock_quantity', '<=', 0);
        elseif ($request->status === 'low') $query->whereRaw('stock_quantity > 0 AND stock_quantity <= min_stock_alert');
        elseif ($request->status === 'in') $query->where('stock_quantity', '>', 0);

        if ($request->category_id) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));
        }

        $variants   = $query->orderBy('stock_quantity')->paginate(50)->withQueryString();
        $categories = \App\Models\Category::active()->orderBy('name_ar')->get();
        $stats      = $this->reports->getDashboardStats();

        $totals = [
            'total_variants' => \App\Models\ProductVariant::where('is_active', true)->count(),
            'in_stock'       => \App\Models\ProductVariant::where('stock_quantity', '>', 0)->count(),
            'low_stock'      => $stats['low_stock_variants'],
            'out_of_stock'   => $stats['out_of_stock_variants'],
        ];

        return view('admin.reports.inventory', compact('variants', 'categories', 'totals'));
    }
}
