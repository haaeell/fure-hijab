<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature   = 'orders:expire-unpaid';
    protected $description = 'Batalkan pesanan pending yang sudah melewati batas waktu pembayaran';

    public function handle(): void
    {
        $orders = Order::with('payment')
            ->where('status', 'pending')
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $payment   = $order->payment;
            $expiresAt = $payment?->expired_at ?? $order->created_at->addDay();

            if ($expiresAt->isFuture()) {
                continue;
            }

            $order->update([
                'status'              => 'cancelled',
                'cancellation_reason' => 'Batas waktu pembayaran habis.',
                'cancelled_at'        => now(),
                'cancelled_by'        => 'system',
            ]);

            $payment?->update(['status' => 'expired']);

            $count++;
        }

        $this->info("Selesai: {$count} pesanan dibatalkan.");
    }
}
