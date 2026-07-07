<?php

namespace App\Traits;

use App\Models\Order;

trait ExpiresUnpaidOrders
{
    protected function expireUnpaidOrders(): void
    {
        Order::with('payment')
            ->where('status', 'pending')
            ->get()
            ->each(function (Order $order) {
                $payment   = $order->payment;

                if ($payment?->payment_channel === 'manual' && in_array($payment->status, ['under_review', 'success'], true)) {
                    return;
                }

                $expiresAt = $payment?->expired_at ?? $order->created_at->addDay();

                if ($expiresAt->isFuture()) {
                    return;
                }

                $order->update([
                    'status'              => 'cancelled',
                    'cancellation_reason' => 'Batas waktu pembayaran habis.',
                    'cancelled_at'        => now(),
                    'cancelled_by'        => 'system',
                ]);

                $payment?->update(['status' => 'expired']);
            });
    }
}
