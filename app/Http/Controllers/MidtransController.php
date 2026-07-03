<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Masuk!', $request->all());

        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $request->order_id)->first();
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $payment = Payment::where('midtrans_order_id', $request->order_id)->first();
        if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        if (
            $transactionStatus == 'settlement' ||
            ($transactionStatus == 'capture' && $fraudStatus == 'accept')
        ) {
            DB::transaction(function () use ($order, $payment, $request) {
                if ($order->status !== 'processing') {
                    foreach ($order->items as $item) {
                        if ($item->variant_id) {
                            $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                            if ($variant) {
                                $variant->update(['stock' => max(0, $variant->stock - $item->qty)]);
                                // Sync stok ke parent produk
                                Product::where('id', $variant->product_id)
                                    ->update(['stock' => ProductVariant::where('product_id', $variant->product_id)->sum('stock')]);
                            }
                        } else {
                            $product = Product::lockForUpdate()->find($item->product_id);
                            if ($product) {
                                $product->update(['stock' => max(0, $product->stock - $item->qty)]);
                            }
                        }
                    }
                }
                $order->update(['status' => 'processing']);
                $payment->update([
                    'status'         => 'success',
                    'paid_at'        => now(),
                    'payment_method' => $request->payment_type,
                ]);
            });
        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $transactionStatus === 'expire'
                    ? 'Batas waktu pembayaran habis.'
                    : 'Pembayaran dibatalkan atau ditolak.',
                'cancelled_at' => now(),
                'cancelled_by' => 'system',
            ]);
            $payment->update([
                'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
            ]);
        }

        return response()->json(['message' => 'Success']);
    }

    public function finish(Request $request)
    {
        $orderNumber = $request->query('order_id');
        if ($orderNumber) {
            return redirect()->route('order.history.show', $orderNumber);
        }
        return redirect()->route('order.history');
    }

    public function error(Request $request)
    {
        return redirect()->route('order.history');
    }
}
