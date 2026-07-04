<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Services\ShipmentTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $this->expireUnpaidOrders();

        $orders = Order::with(['items.product.images', 'items.variant', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $filteredOrders = $request->filled('status')
            ? $orders->where('status', $request->status)
            : $orders;

        return view('user.order.index', compact('orders', 'filteredOrders'));
    }

    public function show(string $orderNumber)
    {
        $this->expireUnpaidOrders();

        $order = Order::with([
            'items.product.category',
            'items.product.images',
            'items.variant.attributes',
            'items.review', // tambah ini
            'address',
            'shipment',
            'payment'
        ])
            ->where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $tracking = $order->shipment?->tracking_history;

        return view('user.order.show', compact('order', 'tracking'));
    }

    public function trackShipment(string $orderNumber, ShipmentTrackingService $trackingService)
    {
        $order = Order::with('shipment')
            ->where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if (!$order->shipment || blank($order->shipment->resi)) {
            return back()->with('error', 'Nomor resi belum tersedia.');
        }

        try {
            $tracking = $trackingService->trackShipment($order->shipment);
        } catch (\Throwable $e) {
            return back()->with('error', 'Tracking Biteship gagal: ' . $e->getMessage());
        }

        $biteshipStatus = $trackingService->statusCode($tracking);

        $order->shipment->update([
            'tracking_history' => $tracking,
            'tracked_at'       => now(),
            'status'           => $biteshipStatus ?? $order->shipment->status,
        ]);

        return back()->with('success', 'Tracking resi berhasil diperbarui.');
    }

    public function markAsCompleted(string $orderNumber)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'shipped')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $order->update([
            'status' => 'delivered'
        ]);

        return redirect()->route('order.history.show', $orderNumber)->with('success', 'Pesanan berhasil dikonfirmasi sebagai selesai.');
    }

    public function cancel(Request $request, string $orderNumber)
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $order = Order::with('payment')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->status === 'pending') {
            $order->restoreStock();
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => 'customer',
        ]);

        if ($order->payment && $order->payment->status === 'pending') {
            $order->payment->update(['status' => 'failed']);
        }

        return redirect()->route('order.history.show', $order->order_number)->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:500',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $item = \App\Models\OrderItem::findOrFail($request->order_item_id);
        $order = $item->order;

        if (Review::where('order_item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Sudah pernah direview'], 422);
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $item->product_id,
            'order_item_id' => $item->id,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
            'images'        => $imagePaths ?: null,
            'is_verified'   => true,
        ]);

        return response()->json(['message' => 'Review berhasil dikirim']);
    }

    private function expireUnpaidOrders(): void
    {
        Order::with('payment')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->get()
            ->each(function (Order $order) {
                $expiresAt = $order->payment?->expired_at ?: $order->created_at->copy()->addDay();

                if ($expiresAt->isFuture()) {
                    return;
                }

                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Batas waktu pembayaran habis.',
                    'cancelled_at' => now(),
                    'cancelled_by' => 'system',
                ]);

                $order->payment?->update(['status' => 'expired']);
            });
    }
}
