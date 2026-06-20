<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiteshipWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        if (!$this->validSecret($request)) {
            Log::warning('Biteship webhook rejected by secret validation.', [
                'ip' => $request->ip(),
                'headers' => $this->safeHeaders($request),
            ]);

            return response()->json(['message' => 'Unauthorized webhook.'], 403);
        }

        $payload = $this->payload($request);
        $data = $this->eventData($payload);

        Log::info('Biteship webhook received.', [
            'event' => $payload['event'] ?? $data['event'] ?? null,
            'order_id' => $data['order_id'] ?? $payload['order_id'] ?? null,
            'tracking_id' => $data['courier_tracking_id'] ?? $payload['courier_tracking_id'] ?? $data['id'] ?? null,
            'waybill_id' => $data['courier_waybill_id'] ?? $data['waybill_id'] ?? $payload['courier_waybill_id'] ?? null,
            'status' => $data['status'] ?? $payload['status'] ?? null,
            'payload' => $payload,
        ]);

        $shipment = $this->findShipment($data, $payload);

        if (!$shipment) {
            Log::warning('Biteship webhook received without matching shipment.', [
                'payload' => $payload,
            ]);

            return response()->json([
                'message' => 'Webhook diterima, tetapi shipment tidak ditemukan.',
            ], 202);
        }

        DB::transaction(function () use ($shipment, $payload, $data) {
            $shipment->loadMissing('order');

            $rawStatus = $this->firstFilled($data, [
                'status',
                'order_status',
                'tracking_status',
                'courier.status',
                'courier.tracking_status',
            ]);
            $shipmentStatus = $this->shipmentStatus($rawStatus);
            $waybill = $this->waybill($data);
            $labelUrl = $this->firstFilled($data, [
                'label_url',
                'courier.label_url',
                'courier.waybill_label_url',
            ]);
            $cost = $this->firstFilled($data, [
                'price',
                'order_price',
                'shipping_price',
                'courier_price',
                'cost',
                'courier.price',
            ]);
            $history = $this->trackingHistory($data, $payload);

            $updates = [
                'biteship_payload' => $payload,
                'tracked_at' => now(),
            ];

            if ($shipmentStatus) {
                $updates['status'] = $shipmentStatus;
            }

            if ($waybill) {
                $updates['resi'] = $waybill;
            }

            if ($labelUrl) {
                $updates['label_url'] = $labelUrl;
            }

            if (filled($cost) && is_numeric($cost) && (int) $cost > 0) {
                $updates['cost'] = (int) $cost;
            }

            if ($history) {
                $updates['tracking_history'] = $history;
            }

            $shipment->update($updates);

            if ($shipment->order && $shipmentStatus) {
                $orderStatus = $this->orderStatus($shipmentStatus, $shipment->order->status);

                if ($orderStatus) {
                    $orderUpdates = ['status' => $orderStatus];

                    if ($orderStatus === 'cancelled' && !in_array($shipment->order->status, ['cancelled', 'refunded'], true)) {
                        $cancelNote = $this->firstFilled($data, ['description', 'note', 'message', 'reason', 'cancellation_reason'])
                            ?: 'Pengiriman dibatalkan oleh kurir. Silakan hubungi admin untuk informasi lebih lanjut.';

                        $orderUpdates['cancellation_reason'] = $cancelNote;
                        $orderUpdates['cancelled_at']        = now();
                        $orderUpdates['cancelled_by']        = 'system';
                    }

                    $shipment->order->update($orderUpdates);
                }
            }
        });

        Log::info('Biteship webhook processed.', [
            'shipment_id' => $shipment->id,
            'order_id' => $shipment->order_id,
            'biteship_order_id' => $shipment->biteship_order_id,
            'resi' => $shipment->resi,
            'shipment_status' => $shipment->fresh()->status,
            'order_status' => $shipment->order?->fresh()?->status,
        ]);

        return response()->json(['message' => 'Webhook Biteship berhasil diproses.']);
    }

    private function payload(Request $request): array
    {
        $payload = $request->json()->all() ?: $request->all();

        if (!empty($payload)) {
            return $payload;
        }

        $raw = $request->getContent();
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function safeHeaders(Request $request): array
    {
        return collect($request->headers->all())
            ->except(['authorization', 'cookie', 'x-csrf-token'])
            ->map(fn ($value) => is_array($value) ? implode(', ', $value) : $value)
            ->all();
    }

    private function validSecret(Request $request): bool
    {
        $secret = config('services.biteship.webhook_secret');

        if (blank($secret)) {
            return true;
        }

        $provided = $request->bearerToken()
            ?: $request->header('X-Biteship-Webhook-Secret')
            ?: $request->header('X-Webhook-Secret')
            ?: $request->header('X-Webhook-Token')
            ?: $request->query('token');

        return filled($provided) && hash_equals((string) $secret, (string) $provided);
    }

    private function eventData(array $payload): array
    {
        $data = $payload['data'] ?? $payload['order'] ?? $payload['tracking'] ?? $payload;

        return is_array($data) ? $data : $payload;
    }

    private function findShipment(array $data, array $payload): ?Shipment
    {
        $biteshipOrderId = $this->firstFilled($data, [
            'id',
            'order_id',
            'biteship_order_id',
        ]) ?: $this->firstFilled($payload, [
            'id',
            'order_id',
            'biteship_order_id',
            'data.id',
        ]);

        $waybill = $this->waybill($data) ?: $this->waybill($payload);
        $reference = $this->firstFilled($data, [
            'reference_id',
            'merchant_order_id',
            'external_id',
        ]) ?: $this->firstFilled($payload, [
            'reference_id',
            'merchant_order_id',
            'external_id',
        ]);

        if ($biteshipOrderId) {
            $shipment = Shipment::where('biteship_order_id', $biteshipOrderId)->first();

            if ($shipment) {
                return $shipment;
            }
        }

        if ($waybill) {
            $shipment = Shipment::where('resi', $waybill)->first();

            if ($shipment) {
                return $shipment;
            }
        }

        if ($reference) {
            $order = Order::where('order_number', $reference)->first();

            if ($order) {
                return $order->shipment;
            }
        }

        return null;
    }

    private function waybill(array $data): ?string
    {
        return $this->firstFilled($data, [
            'courier_waybill_id',
            'data.courier_waybill_id',
            'waybill_id',
            'waybill_number',
            'courier.waybill_id',
            'courier.waybill_number',
        ]);
    }

    private function shipmentStatus(?string $status): ?string
    {
        $status = strtolower(trim((string) $status));

        return match (true) {
            $status === ''                                                               => null,
            str_contains($status, 'deliver') || str_contains($status, 'complete')      => 'delivered',
            str_contains($status, 'cancel') || str_contains($status, 'reject')         => 'cancelled',
            str_contains($status, 'fail')                                               => 'failed',
            str_contains($status, 'return')                                             => 'returned',
            str_contains($status, 'pickup') || str_contains($status, 'pick_up')        => 'picked_up',
            str_contains($status, 'transit') || str_contains($status, 'ship')          => 'in_transit',
            str_contains($status, 'process') || str_contains($status, 'dropping')      => 'in_transit',
            default                                                                     => $status,
        };
    }

    private function orderStatus(string $shipmentStatus, string $currentStatus): ?string
    {
        if (in_array($currentStatus, ['cancelled', 'refunded', 'delivered'], true)) {
            return null;
        }

        return match ($shipmentStatus) {
            'delivered'               => 'delivered',
            'in_transit', 'picked_up' => 'shipped',
            'cancelled', 'failed'     => 'cancelled',
            default                   => null,
        };
    }

    private function trackingHistory(array $data, array $payload): ?array
    {
        $history = $data['history']
            ?? $data['histories']
            ?? $data['tracking_history']
            ?? $data['courier']['history']
            ?? $payload['history']
            ?? null;

        if (is_array($history)) {
            return $history;
        }

        $status = $this->firstFilled($data, ['status', 'order_status', 'tracking_status']);

        if (!$status) {
            return null;
        }

        return [[
            'source' => 'biteship_webhook',
            'status' => $status,
            'description' => $this->firstFilled($data, ['description', 'note', 'message']) ?: $this->trackingStatusLabel($status),
            'created_at' => $this->firstFilled($data, ['updated_at', 'created_at']) ?: now()->toIso8601String(),
        ]];
    }

    private function trackingStatusLabel(string $status): string
    {
        return match (strtolower(trim($status))) {
            'confirmed' => 'Pesanan pengiriman sudah dikonfirmasi.',
            'allocated' => 'Kurir sudah dialokasikan untuk pesanan ini.',
            'picking_up', 'pickup', 'picked_up' => 'Paket sedang dijemput oleh kurir.',
            'dropping_off', 'in_transit', 'on_delivery' => 'Paket sedang dalam perjalanan.',
            'delivered' => 'Paket sudah diterima oleh penerima.',
            'cancelled', 'canceled' => 'Pengiriman dibatalkan.',
            'returned' => 'Paket dikembalikan.',
            'failed' => 'Pengiriman gagal.',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function firstFilled(array $data, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = Arr::get($data, $key);

            if (filled($value)) {
                return $value;
            }
        }

        return null;
    }
}
