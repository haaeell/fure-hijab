<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        $tracking = null;
        if ($order->shipment && $order->shipment->resi) {
            $tracking = $this->getTrackingInfo(
                $order->shipment->resi,
                $order->shipment->courier
            );
        }

        return view('user.order.show', compact('order', 'tracking'));
    }

    private function getTrackingInfo($waybill, $courier)
    {
        if (str_contains($waybill, 'DUMMY')) {
            return [
                'manifest' => [
                    [
                        'manifest_description' => 'Pesanan sedang diproses di gudang pusat Al-Hayya',
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDays(2)->format('Y-m-d'),
                        'manifest_time' => '09:00'
                    ],
                    [
                        'manifest_description' => 'Paket telah diserahkan ke kurir ' . strtoupper($courier),
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDays(1)->format('Y-m-d'),
                        'manifest_time' => '14:30'
                    ],
                    [
                        'manifest_description' => 'Paket sedang transit di Hub Jakarta Selatan',
                        'city_name' => 'Jakarta',
                        'manifest_date' => now()->format('Y-m-d'),
                        'manifest_time' => '08:15'
                    ],
                    [
                        'manifest_description' => 'Paket dibawa kurir [Sdr. Budi] menuju lokasi penerima',
                        'city_name' => 'Jakarta',
                        'manifest_date' => now()->format('Y-m-d'),
                        'manifest_time' => '10:00'
                    ],
                ]
            ];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'key' => config('services.rajaongkir.api_key'),
            ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/waybill/domestic-waybill', [
                'waybill' => $waybill,
                'courier' => $courier,
            ]);

            return $response->successful() ? $response->json()['data'] : null;
        } catch (\Exception $e) {
            return null;
        }
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

        return redirect()->back()->with('success', 'Pesanan selesai');
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

    public function submitReview(Request $request, Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);
        abort_if($order->status !== 'delivered', 403);

        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:500',
        ]);

        $item = $order->items()->findOrFail($request->order_item_id);

        if (Review::where('order_item_id', $item->id)->exists()) {
            return response()->json(['message' => 'Sudah pernah direview'], 422);
        }

        Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $item->product_id,
            'order_item_id' => $item->id,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
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
