<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | USER - ORDER LIST
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())->with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | USER - ORDER DETAIL
    |--------------------------------------------------------------------------
    */

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'user']);

        return view('orders.show', compact('order'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - ALL ORDERS
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - ORDER DETAIL
    |--------------------------------------------------------------------------
    */

    public function adminShow(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - UPDATE STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ], [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in' => 'Status pesanan yang dipilih tidak valid.',
        ]);

        $currentStatus = $order->status;
        $newStatus = $validated['status'];

        if ($currentStatus === 'completed' && $newStatus !== 'completed') {
            return back()->with('error', 'Order yang sudah selesai tidak dapat diubah kembali.');
        }

        if ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->with('error', 'Order yang sudah dibatalkan tidak dapat diproses kembali.');
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - UPDATE PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,waiting,paid,rejected',
        ], [
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'payment_status.in' => 'Status pembayaran yang dipilih tidak valid.',
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN - COMPLETED ORDERS
    |--------------------------------------------------------------------------
    */

    public function completed(Request $request)
    {
        $query = Order::with(['user', 'items'])->where('status', 'completed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.completed', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL ORDER BY USER
    |--------------------------------------------------------------------------
    */

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Order sudah diproses dan tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            $order->load('items');

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        if ($product->sold_count >= $item->quantity) {
                            $product->decrement('sold_count', $item->quantity);
                        }
                    }
                }
            }

            if ($order->promo_code) {
                $promo = Promo::where('code', $order->promo_code)->first();
                if ($promo && !is_null($promo->quota)) {
                    $promo->increment('quota');
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Order berhasil dibatalkan.');
    }
}