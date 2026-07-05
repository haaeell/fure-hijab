<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;
use Throwable;

class MidtransPaymentSyncService
{
    public function sync(Order $order): ?Payment
    {
        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment || !$payment->midtrans_order_id || $payment->status === 'success') {
            return $payment;
        }

        $this->configureMidtrans();

        try {
            $status = Transaction::status($payment->midtrans_order_id);
        } catch (Throwable $th) {
            Log::warning('Midtrans status sync failed', [
                'order_id' => $order->order_number,
                'message' => $th->getMessage(),
            ]);

            return $payment;
        }

        $payload = json_decode(json_encode($status), true) ?: [];

        return $this->applyStatus($order, $payment, $payload);
    }

    public function applyStatus(Order $order, Payment $payment, array $payload): Payment
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if ($this->isPaid($transactionStatus, $fraudStatus)) {
            DB::transaction(function () use ($order, $payment, $payload) {
                if (!in_array($order->status, ['processing', 'shipped', 'delivered'], true)) {
                    $order->update(['status' => 'processing']);
                }

                $payment->update([
                    'status' => 'success',
                    'paid_at' => $payment->paid_at ?: now(),
                    'payment_method' => $payload['payment_type'] ?? $payment->payment_method,
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
                    'payload' => $payload,
                ]);
            });

            return $payment->fresh();
        }

        if (in_array($transactionStatus, ['cancel', 'expire', 'deny'], true)) {
            DB::transaction(function () use ($order, $payment, $payload, $transactionStatus) {
                if ($order->status === 'pending') {
                    $order->restoreStock();
                }

                if (!in_array($order->status, ['cancelled', 'refunded'], true)) {
                    $order->update([
                        'status' => 'cancelled',
                        'cancellation_reason' => $transactionStatus === 'expire'
                            ? 'Batas waktu pembayaran habis.'
                            : 'Pembayaran dibatalkan atau ditolak.',
                        'cancelled_at' => now(),
                        'cancelled_by' => 'system',
                    ]);
                }

                $payment->update([
                    'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                    'payment_method' => $payload['payment_type'] ?? $payment->payment_method,
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
                    'payload' => $payload,
                ]);
            });

            return $payment->fresh();
        }

        $payment->update([
            'payment_method' => $payload['payment_type'] ?? $payment->payment_method,
            'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
            'payload' => $payload ?: $payment->payload,
        ]);

        return $payment->fresh();
    }

    private function isPaid(?string $transactionStatus, ?string $fraudStatus): bool
    {
        return $transactionStatus === 'settlement'
            || ($transactionStatus === 'capture' && ($fraudStatus === null || $fraudStatus === 'accept'));
    }

    private function configureMidtrans(): void
    {
        Config::$serverKey = (string) Setting::getValue('midtrans_server_key', config('services.midtrans.server_key'));
        Config::$clientKey = (string) Setting::getValue('midtrans_client_key', config('services.midtrans.client_key'));
        Config::$isProduction = $this->settingBool('midtrans_is_production', (bool) config('services.midtrans.is_production'));
        Config::$isSanitized = $this->settingBool('midtrans_is_sanitized', (bool) config('services.midtrans.is_sanitized'));
        Config::$is3ds = $this->settingBool('midtrans_is_3ds', (bool) config('services.midtrans.is_3ds'));
    }

    private function settingBool(string $key, bool $default): bool
    {
        return filter_var(Setting::getValue($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}
