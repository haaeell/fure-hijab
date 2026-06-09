<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
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

            if ($order->status !== 'processing') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $item->variant->decrement('stock', $item->qty);
                    } else {
                        $item->product->decrement('stock', $item->qty);
                    }
                }
            }

            $order->update(['status' => 'processing']);
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'payment_method' => $request->payment_type
            ]);
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
}
