<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest('placed_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('ship_recipient', 'like', "%{$s}%");
            });
        }

        $orders = $query->paginate(25)->withQueryString();
        $statusCounts = Order::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.variant.product', 'shipments', 'payments', 'statusLogs']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $order->update($request->only(['admin_notes', 'courier', 'tracking_number']));
        return back()->with('success', 'Order updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'          => 'required|in:pending_payment,confirmed,processing,shipped,delivered,completed,cancelled,return_requested,refunded',
            'tracking_number' => 'nullable|string',
            'courier'         => 'nullable|string',
            'log_title'       => 'nullable|string|max:255',
            'log_description' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'status'          => $validated['status'],
            'courier'         => $validated['courier'] ?? $order->courier,
            'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
        ]);

        $courierName = $order->courier ?? 'courier';
        // Default timeline title based on status if not specified
        $defaultTitle = match($validated['status']) {
            'confirmed'        => 'Order Confirmed by Store',
            'processing'       => 'To Ship — Seller is preparing your parcel at warehouse',
            'shipped'          => "To Receive — Parcel picked up by {$courierName} rider",
            'delivered'        => 'Received — Parcel delivered to buyer',
            'completed'        => 'Order Completed',
            'cancelled'        => 'Order Cancelled',
            'return_requested' => 'Return Requested by Buyer',
            'refunded'         => 'Order Payment Refunded',
            default            => 'Status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])),
        };

        $title = !empty($validated['log_title']) ? $validated['log_title'] : $defaultTitle;
        $description = $validated['log_description'] ?? null;

        $order->statusLogs()->create([
            'status'      => $validated['status'],
            'title'       => $title,
            'description' => $description,
        ]);

        // If shipped, create shipment record
        if ($validated['status'] === 'shipped' && !empty($order->tracking_number)) {
            Shipment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'courier'         => $order->courier ?? 'Lalamove Express',
                    'tracking_number' => $order->tracking_number,
                    'status'          => 'in_transit',
                    'shipped_at'      => now(),
                ]
            );
        }

        if ($validated['status'] === 'delivered') {
            Shipment::where('order_id', $order->id)->update(['status' => 'delivered', 'delivered_at' => now()]);
            $order->update(['payment_status' => $order->payment_method === 'cod' ? 'paid' : $order->payment_status]);
        }

        return back()->with('success', "Order status updated to '{$validated['status']}' and timeline logged.");
    }

    public function packingSlip(Order $order)
    {
        $order->load('items.variant.product');
        return view('admin.orders.packing-slip', compact('order'));
    }
}
