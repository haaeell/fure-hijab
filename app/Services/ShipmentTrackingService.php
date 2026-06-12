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
                'manifest' => [
                    [
                        'manifest_description' => 'Pesanan sedang diproses di gudang pusat FURE',
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDays(2)->format('Y-m-d'),
                        'manifest_time' => '09:00',
                    ],
                    [
                        'manifest_description' => 'Paket telah diserahkan ke kurir ' . strtoupper($courier),
                        'city_name' => 'Bandung',
                        'manifest_date' => now()->subDay()->format('Y-m-d'),
                        'manifest_time' => '14:30',
                    ],
                    [
                        'manifest_description' => 'Paket sedang transit di Hub Jakarta Selatan',
                        'city_name' => 'Jakarta',
                        'manifest_date' => now()->format('Y-m-d'),
                        'manifest_time' => '08:15',
                    ],
                ],
            ];
        }

        return $this->biteship->track($waybill, $courier);
    }

    public function trackShipment(Shipment $shipment): ?array
    {
        if (blank($shipment->courier)) {
            return null;
        }

        $payload = is_array($shipment->biteship_payload) ? $shipment->biteship_payload : [];
        $candidates = collect([
            Arr::get($payload, 'courier_tracking_id'),
            Arr::get($payload, 'tracking_id'),
            Arr::get($payload, 'id'),
            Arr::get($payload, 'data.courier_tracking_id'),
            Arr::get($payload, 'data.tracking_id'),
            Arr::get($payload, 'data.id'),
            Arr::get($payload, 'courier.waybill_id'),
            Arr::get($payload, 'courier_waybill_id'),
            Arr::get($payload, 'waybill_id'),
            $shipment->resi,
        ])->filter()->unique()->values();

        $lastError = null;

        foreach ($candidates as $candidate) {
            try {
                return $this->track($candidate, $shipment->courier);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        if ($lastError) {
            throw new \RuntimeException($lastError);
        }

        return null;
    }

    public function latestStatus(?array $tracking): ?string
    {
        $manifest = $tracking['manifest'] ?? $tracking['history'] ?? [];

        if (!is_array($manifest) || count($manifest) === 0) {
            return $tracking['summary']['status'] ?? $tracking['status'] ?? null;
        }

        $latest = collect($manifest)->sortByDesc(function ($item) {
            return trim(($item['manifest_date'] ?? $item['date'] ?? '') . ' ' . ($item['manifest_time'] ?? $item['time'] ?? ''));
        })->first();

        return $latest['manifest_description'] ?? $latest['description'] ?? ($tracking['summary']['status'] ?? null);
    }
}
