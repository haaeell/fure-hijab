<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Courier;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\UserAddress;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    private const PAYMENT_WINDOW_MINUTES = 1440;

    private function activeCouriers(): array
    {
        return Courier::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'code')
            ->toArray();
    }

    public function index()
    {
        $user = Auth::user();
        $blockedOrder = $this->activeUnpaidOrder($user->id);

        if ($blockedOrder) {
            $expiresAt = $this->paymentExpiresAt($blockedOrder);

            return redirect()
                ->route('order.history.show', $blockedOrder->order_number)
                ->with('error', 'Kamu masih punya pesanan yang belum dibayar. Selesaikan atau batalkan dulu sebelum membuat pesanan baru. Batas bayar sampai ' . $expiresAt->format('d M Y H:i') . '.');
        }

        $cart = Cart::with(['items.product.images', 'items.variant.attributes'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $address      = $user->addresses()->where('is_default', true)->first();
        $carts        = $cart->items;
        $total_price  = 0;
        $total_weight = 0;

        foreach ($carts as $item) {
            $total_price  += (float) $item->price * (int) $item->qty;
            $total_weight += $this->itemWeight($item) * (int) $item->qty;
        }

        // Ambil kupon dari session jika ada
        $appliedCoupon   = null;
        $discountAmount  = 0;

        if (session('coupon_code')) {
            $appliedCoupon = Coupon::where('code', session('coupon_code'))->first();
            if ($appliedCoupon) {
                $discountAmount = $appliedCoupon->calculateDiscount($total_price);
            }
        }

        $couriers = $this->activeCouriers();

        return view('user.checkout.index', compact(
            'carts',
            'total_price',
            'total_weight',
            'address',
            'couriers',
            'appliedCoupon',   // ← tambah
            'discountAmount',  // ← tambah
        ));
    }

    // --- METHOD BARU: Apply Coupon ---
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $user    = Auth::user();
        $cart    = Cart::with('items.product')->where('user_id', $user->id)->first();
        $subtotal = $cart
            ? $cart->items->reduce(fn($carry, $item) => $carry + ($item->price * $item->qty), 0)
            : 0;

        $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Kode kupon tidak ditemukan.']);
        }

        $validation = $coupon->validate($subtotal);

        if (!$validation['valid']) {
            return response()->json(['success' => false, 'message' => $validation['message']]);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session(['coupon_code' => $coupon->code]);

        return response()->json([
            'success'         => true,
            'message'         => 'Kupon berhasil diterapkan!',
            'coupon_name'     => $coupon->name,
            'coupon_type'     => $coupon->type,
            'coupon_value'    => $coupon->value,
            'discount_amount' => $discount,
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return response()->json(['success' => true]);
    }

    public function setAddress(Request $request)
    {
        $request->validate(['address_id' => 'required|exists:user_addresses,id']);

        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('checkout.index');
    }

    public function checkOngkir(Request $request, BiteshipService $biteship)
    {
        $request->validate([
            'couriers' => 'required|array|min:1',
        ]);

        $address = Auth::user()->addresses()->where('is_default', true)->first();

        if (!$address) {
            return response()->json(['error' => 'Alamat pengiriman belum dipilih.'], 400);
        }

        if (!$address->postal_code) {
            return response()->json(['error' => 'Kode pos alamat belum diisi. Biteship membutuhkan kode pos tujuan.'], 400);
        }

        $cart = Cart::with(['items.product', 'items.variant'])
            ->where('user_id', Auth::id())
            ->first();

        $weight = $cart ? $this->cartWeight($cart) : 0;

        if ($weight <= 0) {
            return response()->json(['error' => 'Berat paket tidak valid. Pastikan produk memiliki berat.'], 422);
        }

        try {
            $rates = $biteship->rates(
                $request->couriers,
                [
                    'contact_name' => $address->receiver_name ?: Auth::user()->name,
                    'contact_phone' => $address->phone ?: Auth::user()->phone,
                    'address' => trim($address->address . ', ' . $address->subdistrict . ', ' . $address->district . ', ' . $address->city . ', ' . $address->province),
                    'area_id' => $address->biteship_area_id,
                    'postal_code' => $address->postal_code,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                ],
                $this->biteshipItems($cart)
            );

            $services = $biteship->normalizeRates($rates);

            if (empty($services)) {
                return response()->json(['error' => 'Tidak ada layanan pengiriman tersedia untuk rute ini.'], 404);
            }

            return response()->json($services);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function searchDestination(Request $request, BiteshipService $biteship)
    {
        $request->validate([
            'search' => 'required|string|min:3'
        ]);

        try {
            return response()->json($biteship->searchAreas($request->search));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    protected function initMidtrans()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function store(Request $request)
    {
        $blockedOrder = $this->activeUnpaidOrder(Auth::id());

        if ($blockedOrder) {
            return redirect()
                ->route('order.history.show', $blockedOrder->order_number)
                ->with('error', 'Kamu masih punya pesanan yang belum dibayar. Selesaikan atau batalkan dulu sebelum membuat pesanan baru.');
        }

        $request->validate([
            'address_id'      => 'required|exists:user_addresses,id',
            'courier_code'    => 'required|string',
            'courier_service' => 'required|string',
            'shipping_cost'   => 'required|integer|min:0',
            'shipping_etd'    => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $user = Auth::user();
        $cart = Cart::with(['items.product', 'items.variant'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = $cart->items->reduce(function ($carry, $item) {
            return $carry + ($item->price * $item->qty);
        }, 0);
        $totalWeight = $this->cartWeight($cart);

        $discountAmount = 0;
        $couponId       = null;
        $coupon         = null;

        if (session('coupon_code')) {
            $coupon = Coupon::where('code', session('coupon_code'))->first();
            if ($coupon) {
                $validation = $coupon->validate($subtotal);
                if ($validation['valid']) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponId       = $coupon->id;
                }
            }
        }

        $grandTotal = $subtotal - $discountAmount + $request->shipping_cost;
        $orderNumber = Order::generateOrderNumber();

        $order = DB::transaction(function () use ($request, $user, $cart, $subtotal, $grandTotal, $discountAmount, $couponId, $coupon, $orderNumber, $totalWeight) {

            $order = Order::create([
                'user_id'        => $user->id,
                'order_number'   => $orderNumber,
                'subtotal'       => $subtotal,
                'shipping_cost'  => $request->shipping_cost,
                'discount'       => $discountAmount,
                'total'          => $grandTotal,
                'notes'          => $request->notes,
                'coupon_id'      => $couponId,
                'status'         => 'pending',
            ]);

            $userAddress = UserAddress::find($request->address_id);
            OrderAddress::create([
                'order_id'       => $order->id,
                'type'           => 'shipping',
                'receiver_name'  => $userAddress->receiver_name ?? $user->name,
                'phone'          => $userAddress->phone,
                'address'        => $userAddress->address,
                'province'       => $userAddress->province,
                'city'           => $userAddress->city,
                'district'       => $userAddress->district,
                'subdistrict'    => $userAddress->subdistrict,
                'postal_code'    => $userAddress->postal_code,
                'biteship_area_id' => $userAddress->biteship_area_id,
                'latitude'       => $userAddress->latitude,
                'longitude'      => $userAddress->longitude,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'variant_id'    => $item->variant_id,
                    'product_name'  => $item->product->name,
                    'variant_name'  => $item->variant?->name,
                    'qty'           => $item->qty,
                    'price'         => $item->price,
                    'subtotal'      => $item->price * $item->qty,
                ]);
            }

            Shipment::create([
                'order_id'          => $order->id,
                'courier'           => $request->courier_code,
                'service'           => $request->courier_service,
                'service_code'      => $request->courier_service,
                'cost'              => $request->shipping_cost,
                'total_weight'      => $totalWeight,
                'status'            => 'pending',
                'estimated_days'    => $request->shipping_etd,
            ]);

            $this->initMidtrans();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderNumber,
                    'gross_amount' => (int) $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->phone ?? $user->phone,
                ],
                'item_details' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'price' => (int) $item->price,
                        'quantity' => $item->qty,
                        'name' => substr($item->product->name, 0, 50)
                    ];
                })->toArray()
            ];

            if ($request->shipping_cost > 0) {
                $params['item_details'][] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $request->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim'
                ];
            }

            $snapToken = Snap::getSnapToken($params);

            Payment::create([
                'order_id' => $order->id,
                'midtrans_order_id' => $orderNumber,
                'amount' => $grandTotal,
                'status' => 'pending',
                'snap_token' => $snapToken,
                'expired_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
            ]);

            $cart->items()->delete();
            session()->forget('coupon_code');

            return $order;
        });

        return redirect()->route('order.history.show', $order->order_number)->with('success', 'Pesanan berhasil dibuat! No. Pesanan: ' . $order->order_number);
    }

    public function validateVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        $code = strtoupper(trim($request->code));
        $userId = Auth::id();

        $cart = Cart::with('items')->where('user_id', $userId)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Keranjang kosong'], 400);
        }

        $subtotal = $cart->items->sum(fn($i) => $i->price * $i->qty);

        $coupon = DB::table('coupons')
            ->where('code', $code)
            ->where('is_active', true)
            ->where(function ($q) use ($subtotal) {
                $q->where('min_purchase', 0)->orWhere('min_purchase', '<=', $subtotal);
            })
            ->where(function ($q) {
                $q->whereNull('quota')->orWhere('quota', '>', DB::table('coupons')->where('code', $code)->value('used_count'));
            })
            ->where(function ($q) {
                $q->whereNull('started_at')->orWhere('started_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json(['error' => 'Voucher tidak valid atau expired'], 400);
        }

        $discountValue = $coupon->type === 'percent'
            ? min(($subtotal * $coupon->value / 100), $coupon->max_discount ?? PHP_INT_MAX)
            : $coupon->value;

        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $discountValue,
            'formatted_discount' => 'Rp' . number_format($discountValue, 0, ',', '.')
        ]);
    }

    public function checkPaymentStatus(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $payment = Payment::where('order_id', $order->id)->first();
        return response()->json([
            'status' => $payment?->status,
            'order_status' => $order->status,
            'expired_at' => $payment?->expired_at?->toIso8601String(),
        ]);
    }

    private function cartWeight(Cart $cart): int
    {
        return (int) $cart->items->sum(function ($item) {
            return $this->itemWeight($item) * (int) $item->qty;
        });
    }

    private function itemWeight($item): int
    {
        $weight = $item->variant?->weight ?: $item->product?->weight ?: 10;

        return max(1, (int) ceil((float) $weight));
    }

    private function biteshipItems(Cart $cart): array
    {
        return $cart->items->map(function ($item) {
            return [
                'name' => $item->product->name,
                'description' => $item->variant?->name ?: $item->product->name,
                'value' => (int) $item->price,
                'quantity' => (int) $item->qty,
                'weight' => $this->itemWeight($item),
                'length' => 20,
                'width' => 20,
                'height' => 5,
            ];
        })->values()->all();
    }

    private function activeUnpaidOrder(int $userId): ?Order
    {
        $orders = Order::with('payment')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($orders as $order) {
            $expiresAt = $this->paymentExpiresAt($order);

            if ($expiresAt->isPast()) {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Batas waktu pembayaran habis.',
                    'cancelled_at' => now(),
                    'cancelled_by' => 'system',
                ]);
                $order->payment?->update(['status' => 'expired']);
                continue;
            }

            return $order;
        }

        return null;
    }

    private function paymentExpiresAt(Order $order)
    {
        return $order->payment?->expired_at ?: $order->created_at->copy()->addMinutes(self::PAYMENT_WINDOW_MINUTES);
    }
}
