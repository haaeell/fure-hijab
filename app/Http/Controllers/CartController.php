<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Cart::with(['items.product.category', 'items.product.images', 'items.variant'])
            ->where('user_id', Auth::id())
            ->first();

        $carts = $cart ? $cart->items : collect();

        $total_price = $carts->sum(function ($item) {
            return $item->price * $item->qty;
        });

        return view('user.cart.index', compact('total_price', 'carts'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        $variantId = $request->product_variant_id;
        $price = $product->price;

        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if ($variant) {
                $price = $variant->price;
            }
        }

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'qty' => $cartItem->qty + $request->quantity
            ]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $request->product_id,
                'variant_id' => $variantId,
                'qty'        => $request->quantity,
                'price'      => $price
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan ke keranjang'
        ]);
    }

    public function buyNow(Request $request)
    {
        $blockedOrder = $this->activeUnpaidOrder();

        if ($blockedOrder) {
            return response()->json([
                'status' => 'blocked',
                'message' => 'Kamu masih punya pesanan yang belum dibayar. Selesaikan atau batalkan pesanan tersebut sebelum membuat pesanan baru.',
                'redirect' => route('order.history.show', $blockedOrder->order_number),
            ], 422);
        }

        $this->store($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk siap checkout.',
            'redirect' => route('checkout.index'),
        ]);
    }

    private function activeUnpaidOrder(): ?Order
    {
        $orders = Order::with('payment')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($orders as $order) {
            $payment = $order->payment;
            $expiresAt = $payment?->expired_at ?: $order->created_at->copy()->addDay();

            if ($expiresAt->isPast()) {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Batas waktu pembayaran habis.',
                    'cancelled_at' => now(),
                    'cancelled_by' => 'system',
                ]);
                $payment->update(['status' => 'expired']);
                continue;
            }

            return $order;
        }

        return null;
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->update(['qty' => $request->quantity]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $cartItem->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang');
    }
}
