<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Arr;

class ShipmentTrackingService
{
    public function __construct(private ?BiteshipService $biteship = null)
    {
        $this->biteship ??= app(BiteshipService::class);
    }

    public function track(?string $waybill, ?string $courier): ?array
    {
        return $this->trackPublic($waybill, $courier);
    }

    public function trackPublic(?string $waybill, ?string $courier): ?array
    {
        if (blank($waybill) || blank($courier)) {
            return null;
        }

        if (str_contains(strtoupper($waybill), 'DUMMY')) {
            return [
                'summary' => [
                    'waybill' => $waybill,
                    'courier' => strtoupper($courier),
                    'status' => 'in_transit',
                ],
                'history' => [
                    [
                        'note' => 'Pesanan sedang diproses di gudang pusat FURE',
                        'status' => 'confirmed',
                        'updated_at' => now()->subDays(2)->toIso8601String(),
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDays(2)->format('Y-m-d'),
                        'manifest_time' => '09:00',
                    ],
                    [
                        'note' => 'Paket telah diserahkan ke kurir ' . strtoupper($courier),
                        'status' => 'picked',
                        'updated_at' => now()->subDay()->toIso8601String(),
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDay()->format('Y-m-d'),
                        'manifest_time' => '14:30',
                    ],
                    [
                        'note' => 'Paket sedang transit di Hub Jakarta Selatan',
                        'status' => 'dropping_off',
                        'updated_at' => now()->toIso8601String(),
                        'city_name' => 'Jakarta',
                        'manifest_date' => now()->format('Y-m-d'),
                        'manifest_time' => '08:15',
                    ],
                ],
            ];
        }

        return $this->biteship->trackPublic($waybill, $courier);
    }

    public function trackShipment(Shipment $shipment): ?array
    {
        return $this->trackCustomerShipment($shipment);
    }

    public function trackCustomerShipment(Shipment $shipment): ?array
    {
        if (blank($shipment->courier)) {
            return null;
        }

        $payload = is_array($shipment->biteship_payload) ? $shipment->biteship_payload : [];
        $candidates = collect([
            $shipment->resi,
            Arr::get($payload, 'courier.waybill_id'),
            Arr::get($payload, 'courier_waybill_id'),
            Arr::get($payload, 'waybill_id'),
            Arr::get($payload, 'waybill_number'),
            Arr::get($payload, 'data.courier.waybill_id'),
            Arr::get($payload, 'data.courier_waybill_id'),
            Arr::get($payload, 'data.waybill_id'),
            Arr::get($payload, 'data.waybill_number'),
        ])->filter()->unique()->values();

        $lastError = null;

        foreach ($candidates as $candidate) {
            try {
                return $this->trackPublic($candidate, $shipment->courier);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        if ($lastError) {
            throw new \RuntimeException($lastError);
        }

        return null;
    }

    public function trackAdminShipment(Shipment $shipment): ?array
    {
        $payload = is_array($shipment->biteship_payload) ? $shipment->biteship_payload : [];
        $trackingId = Arr::get($payload, 'courier_tracking_id')
            ?? Arr::get($payload, 'tracking_id')
            ?? Arr::get($payload, 'courier.tracking_id')
            ?? Arr::get($payload, 'data.courier_tracking_id')
            ?? Arr::get($payload, 'data.tracking_id')
            ?? Arr::get($payload, 'data.courier.tracking_id');

        if (blank($trackingId) && Arr::get($payload, 'object') === 'tracking') {
            $trackingId = Arr::get($payload, 'id');
        }

        if (blank($trackingId) && Arr::get($payload, 'data.object') === 'tracking') {
            $trackingId = Arr::get($payload, 'data.id');
        }

        if (blank($trackingId)) {
            throw new \RuntimeException('Tracking ID Biteship belum tersedia untuk endpoint admin.');
        }

        return $this->biteship->trackById($trackingId);
    }

    /**
     * Returns the machine-readable Biteship status code from a tracking payload,
     * e.g. "picking_up", "delivered". Use this when storing to the database.
     */
    public function statusCode(?array $tracking): ?string
    {
        if (!$tracking) return null;
        return $tracking['summary']['status'] ?? $tracking['status'] ?? null;
    }

    /**
     * Returns the latest human-readable description from the tracking manifest.
     * Used only for display purposes.
     */
    public function latestStatus(?array $tracking): ?string
    {
        $manifest = $tracking['manifest'] ?? $tracking['history'] ?? [];

        if (!is_array($manifest) || count($manifest) === 0) {
            return $tracking['summary']['status'] ?? $tracking['status'] ?? null;
        }

        $latest = collect($manifest)->sortByDesc(function ($item) {
            if (!empty($item['updated_at'])) {
                return (string) $item['updated_at'];
            }

            return trim(($item['manifest_date'] ?? $item['date'] ?? '') . ' ' . ($item['manifest_time'] ?? $item['time'] ?? ''));
        })->first();

        return $latest['note'] ?? $latest['manifest_description'] ?? $latest['description'] ?? ($tracking['summary']['status'] ?? null);
    }
}
