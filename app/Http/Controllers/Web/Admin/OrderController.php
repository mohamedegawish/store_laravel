<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private \App\Services\CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $query = Order::with('user')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%"));
            }))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->payment_status, fn($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest();

        $orders   = $query->paginate(20)->withQueryString();
        $statuses = Order::$statuses;
        $paymentStatuses = Order::$paymentStatuses;

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'orderItems.variant', 'user', 'statusHistory.createdBy']);
        $statuses = Order::$statuses;
        $paymentStatuses = Order::$paymentStatuses;
        return view('admin.orders.show', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::$statuses)),
            'notes'  => 'nullable|string|max:500',
        ]);

        $this->orderService->updateStatus($order, $request->status, $request->notes);

        return back()->with('success', 'تم تحديث حالة الطلب');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:' . implode(',', array_keys(Order::$paymentStatuses)),
        ]);

        $this->orderService->updatePaymentStatus($order, $request->payment_status);

        return back()->with('success', 'تم تحديث حالة الدفع');
    }

    public function updateAdminNotes(Request $request, Order $order)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);
        $order->update(['admin_notes' => $request->admin_notes]);
        return back()->with('success', 'تم حفظ الملاحظات');
    }

    public function invoice(Order $order)
    {
        $order->load(['orderItems.product', 'orderItems.variant', 'user']);
        $settings = \App\Models\StoreSetting::current();
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order', 'settings'));
        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }
}
