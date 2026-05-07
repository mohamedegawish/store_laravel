<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $ordersQuery = Order::query()->whereNotIn('status', ['cancelled']);

        return [
            'total_orders'          => Order::count(),
            'total_revenue'         => (float) $ordersQuery->sum('total_amount'),
            'orders_today'          => Order::whereDate('created_at', $today)->count(),
            'revenue_today'         => (float) $ordersQuery->whereDate('created_at', $today)->sum('total_amount'),
            'orders_this_month'     => Order::where('created_at', '>=', $thisMonth)->count(),
            'revenue_this_month'    => (float) $ordersQuery->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'total_customers'       => User::where('role', 'customer')->count(),
            'total_products'        => Product::active()->count(),
            'pending_orders'        => Order::byStatus('pending')->count(),
            'cancelled_orders'      => Order::byStatus('cancelled')->count(),
            'low_stock_variants'    => ProductVariant::whereRaw('stock_quantity > 0 AND stock_quantity <= min_stock_alert')->count(),
            'out_of_stock_variants' => ProductVariant::where('stock_quantity', '<=', 0)->count(),
        ];
    }

    public function getSalesChart(string $period = 'week'): array
    {
        $days = match($period) {
            'week'  => 7,
            'month' => 30,
            'year'  => 365,
            default => 7,
        };

        $data = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels  = [];
        $revenue = [];
        $orders  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date   = now()->subDays($i)->format('Y-m-d');
            $record = $data->firstWhere('date', $date);
            $labels[]  = now()->subDays($i)->format('d/m');
            $revenue[] = $record ? (float) $record->revenue : 0;
            $orders[]  = $record ? (int) $record->orders_count : 0;
        }

        return compact('labels', 'revenue', 'orders');
    }

    public function getTopProducts(int $limit = 10, ?string $from = null, ?string $to = null): \Illuminate\Database\Eloquent\Collection
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->when($from, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('created_at', '>=', $from)))
            ->when($to, fn($q) => $q->whereHas('order', fn($oq) => $oq->where('created_at', '<=', $to)))
            ->with('product:id,name_ar,name_en,images')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function getTopCustomers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit($limit)
            ->get();
    }

    public function getOrdersByStatus(): array
    {
        return Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getRevenueByCategory(int $limit = 8): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->select('categories.name_ar', 'categories.name_en', DB::raw('SUM(order_items.total_price) as revenue'))
            ->groupBy('categories.id', 'categories.name_ar', 'categories.name_en')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getLowStockReport(): \Illuminate\Database\Eloquent\Collection
    {
        return ProductVariant::with('product:id,name_ar,name_en,images,sku')
            ->whereRaw('stock_quantity <= min_stock_alert')
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->get();
    }
}
