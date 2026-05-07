<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index()
    {
        $stats       = $this->reports->getDashboardStats();
        $salesChart  = $this->reports->getSalesChart(request('period', 'week'));
        $topProducts = $this->reports->getTopProducts(5);
        $recentOrders= Order::with('user')->latest()->limit(8)->get();
        $lowStock    = $this->reports->getLowStockReport()->take(8);
        $ordersByStatus = $this->reports->getOrdersByStatus();
        $unreadMessages = ContactMessage::unread()->count();

        return view('admin.dashboard', compact(
            'stats', 'salesChart', 'topProducts',
            'recentOrders', 'lowStock', 'ordersByStatus', 'unreadMessages'
        ));
    }
}
