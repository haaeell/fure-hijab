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
        $cart = Cart::with([
                'items:id,cart_id,product_id,variant_id,qty,price',
                'items.product:id,category_id,name,slug,stock,weight',
                'items.product.category:id,name',
                'items.product.images:id,product_id,image_url,is_primary,sort_order',
                'items.variant:id,product_id,name,stock,weight',
                'items.variant.attributes:id,variant_id,attribute_name,attribute_value',
            ])
            ->where('user_id', Auth::id())
            ->first();

        $carts = $cart ? $cart->items : collect();

        $total_price = $carts->sum(fn($item) => $item->price * $item->qty);

        return view('user.cart.index', compact('total_price', 'carts'));
    }

    public function summary()
    {
        $cart = Cart::with([
                'items:id,cart_id,product_id,variant_id,qty,price',
                'items.product:id,category_id,name,slug',
                'items.product.category:id,name',
                'items.product.images:id,product_id,image_url,is_primary,sort_order',
                'items.variant:id,product_id,name',
                'items.variant.attributes:id,variant_id,attribute_value',
            ])
            ->where('user_id', Auth::id())
            ->first();

        $items = $cart ? $cart->items : collect();
        $subtotal = $items->sum(fn ($item) => $item->price * $item->qty);

        return response()->json([
            'items' => $items->map(function ($item) {
                $image = $item->product->images->firstWhere('is_primary', true)
                    ?? $item->product->images->first();

                return [
                    'id' => $item->id,
                    'name' => $item->product->name,
                    'category' => $item->product->category?->name,
                    'image' => $image ? asset('storage/' . $image->image_url) : 'https://via.placeholder.com/400x533',
                    'variant' => $item->variant
                        ? $item->variant->attributes->pluck('attribute_value')->implode(' | ')
                        : null,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->qty,
                ];
            })->values(),
            'subtotal' => $subtotal,
            'count' => $items->count(),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'quantity'           => 'required|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product   = Product::findOrFail($request->product_id);
        $variantId = $request->product_variant_id;
        $variant   = null;
        $price     = $product->price;

        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if ($variant) {
                $price = $variant->price;
            }
        }

        $availableStock = $variant ? $variant->stock : $product->stock;

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $variantId)
            ->first();

        $currentQty = $cartItem ? $cartItem->qty : 0;
        $newQty     = $currentQty + $request->quantity;

        if ($newQty > $availableStock) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Stok tidak mencukupi. Tersedia: ' . $availableStock . ', di keranjang: ' . $currentQty,
            ], 422);
        }

        if ($cartItem) {
            $cartItem->update(['qty' => $newQty]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $request->product_id,
                'variant_id' => $variantId,
                'qty'        => $request->quantity,
                'price'      => $price,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan ke keranjang',
        ]);
    }

    public function checkout(Request $request)
    {
        $selectedIds = array_filter(array_map('intval', $request->input('selected_items', [])));

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        session(['checkout_selected_items' => $selectedIds]);

        return redirect()->route('checkout.index');
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
        $cartItem = CartItem::with(['product', 'variant'])
            ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        $request->validate(['quantity' => 'required|integer|min:0']);

        if ($request->quantity <= 0) {
            $cartItem->delete();
            return response()->json(['status' => 'deleted']);
        }

        $stock = $cartItem->variant
            ? $cartItem->variant->stock
            : $cartItem->product->stock;

        if ($request->quantity > $stock) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Stok tidak mencukupi. Tersedia: ' . $stock,
            ], 422);
        }

        $cartItem->update(['qty' => $request->quantity]);

        return response()->json([
            'status'    => 'success',
            'qty'       => $request->quantity,
            'subtotal'  => $cartItem->price * $request->quantity,
        ]);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        $cartItem->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang');
    }
}
