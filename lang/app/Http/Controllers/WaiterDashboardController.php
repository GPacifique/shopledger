<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WaiterDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Adjust column names/status values here if your POS uses different names.
        $openOrders = Order::query()
            ->with(['table', 'items.product'])
            ->where('waiter_id', $userId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->latest()
            ->get();

        $readyOrders = $openOrders->where('status', 'ready')->values();

        $unpaidBills = class_exists('App\\Models\\Bill')
            ? \App\Models\Bill::query()->where('waiter_id', $userId)->whereIn('status', ['unpaid', 'partial'])->get()
            : collect();

        $tables = Table::query()->orderBy('number')->get();

        $todaySales = Order::query()
            ->where('waiter_id', $userId)
            ->whereDate('created_at', today())
            ->whereIn('status', ['served', 'paid', 'completed'])
            ->sum('total');

        return view('waiter.dashboard', compact(
            'openOrders', 'readyOrders', 'unpaidBills', 'tables', 'todaySales'
        ) + ['notifications' => collect(), 'shift' => null, 'currency' => 'RWF']);
    }

    public function downloadOrder(Order $order)
    {
        abort_unless($order->waiter_id === auth()->id(), 403);
        $order->load(['table', 'waiter', 'items.product']);

        return Pdf::loadView('waiter.pdf.order', [
            'order' => $order,
            'currency' => 'RWF',
        ])->download('order-' . ($order->order_number ?? $order->id) . '.pdf');
    }

    public function printOrder(Order $order)
    {
        abort_unless($order->waiter_id === auth()->id(), 403);
        $order->load(['table', 'waiter', 'items.product']);
        return view('waiter.pdf.order', ['order' => $order, 'currency' => 'RWF']);
    }

    public function downloadBill($bill)
    {
        $bill = \App\Models\Bill::with(['table', 'waiter', 'items.product', 'order.items.product'])->findOrFail($bill);
        abort_unless($bill->waiter_id === auth()->id(), 403);

        return Pdf::loadView('waiter.pdf.bill', [
            'bill' => $bill,
            'currency' => 'RWF',
        ])->download('bill-' . ($bill->bill_number ?? $bill->id) . '.pdf');
    }

    public function printBill($bill)
    {
        $bill = \App\Models\Bill::with(['table', 'waiter', 'items.product', 'order.items.product'])->findOrFail($bill);
        abort_unless($bill->waiter_id === auth()->id(), 403);
        return view('waiter.pdf.bill', ['bill' => $bill, 'currency' => 'RWF']);
    }
}
