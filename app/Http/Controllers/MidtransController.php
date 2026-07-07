<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = (string) $request->input('order_id', '');

        Log::info('Midtrans callback received', [
            'order_id' => $orderId,
            'transaction_status' => $request->input('transaction_status'),
            'status_code' => $request->input('status_code'),
        ]);

        if ($this->isNotificationTest($orderId)) {
            Log::info('Midtrans notification test accepted', [
                'order_id' => $orderId,
            ]);

            return response()->json(['message' => 'Midtrans notification test accepted']);
        }

        if (!$request->filled(['order_id', 'status_code', 'gross_amount', 'signature_key'])) {
            Log::warning('Midtrans callback missing required fields', [
                'order_id' => $orderId,
                'keys' => array_keys($request->all()),
            ]);

            return response()->json(['message' => 'Invalid notification payload'], 400);
        }

        $serverKey = $this->midtransServerKey();
        $hashed = hash('sha512', $orderId . $request->status_code . $request->gross_amount . $serverKey);

        if (!hash_equals($hashed, (string) $request->signature_key)) {
            Log::warning('Midtrans callback invalid signature', [
                'order_id' => $orderId,
                'status_code' => $request->input('status_code'),
                'gross_amount' => $request->input('gross_amount'),
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            Log::warning('Midtrans callback order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $payment = Payment::where('midtrans_order_id', $orderId)->first();
        if (!$payment) {
            Log::warning('Midtrans callback payment not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        if (
            $transactionStatus == 'settlement' ||
            ($transactionStatus == 'capture' && $fraudStatus == 'accept')
        ) {
            DB::transaction(function () use ($order, $payment, $request) {
                // Stok sudah dikurangi saat checkout - cukup update status
                if ($order->status !== 'processing') {
                    $order->update(['status' => 'processing']);
                }
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'payment_channel' => 'midtrans',
                    'payment_method' => $request->payment_type,
                    'midtrans_transaction_id' => $request->transaction_id,
                    'payload' => $request->all(),
                ]);
            });
        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            if ($order->status === 'pending') {
                $order->restoreStock();
            }
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
                'payment_channel' => 'midtrans',
                'payment_method' => $request->payment_type,
                'midtrans_transaction_id' => $request->transaction_id,
                'payload' => $request->all(),
            ]);
        } else {
            $payment->update([
                'payment_channel' => 'midtrans',
                'payment_method' => $request->payment_type,
                'midtrans_transaction_id' => $request->transaction_id,
                'payload' => $request->all(),
            ]);
        }

        return response()->json(['message' => 'Success']);
    }

    private function isNotificationTest(string $orderId): bool
    {
        return str_starts_with($orderId, 'payment_notif_test_');
    }

    private function midtransServerKey(): string
    {
        return (string) Setting::getValue('midtrans_server_key', config('services.midtrans.server_key'));
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
