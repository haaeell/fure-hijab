<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class ShipmentTrackingService
{
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

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.api_key'),
            ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/waybill/domestic-waybill', [
                'waybill' => $waybill,
                'courier' => $courier,
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json('data');
        } catch (Throwable) {
            return null;
        }
    }

    public function latestStatus(?array $tracking): ?string
    {
        $manifest = $tracking['manifest'] ?? [];

        if (!is_array($manifest) || count($manifest) === 0) {
            return $tracking['summary']['status'] ?? null;
        }

        $latest = collect($manifest)->sortByDesc(function ($item) {
            return trim(($item['manifest_date'] ?? $item['date'] ?? '') . ' ' . ($item['manifest_time'] ?? $item['time'] ?? ''));
        })->first();

        return $latest['manifest_description'] ?? $latest['description'] ?? ($tracking['summary']['status'] ?? null);
    }
}
