<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Throwable;

class BiteshipService
{
    private const BASE_URL = 'https://api.biteship.com/v1';

    public function rates(array $couriers, array $destination, array $items): array
    {
        $payload = [
            'origin_contact_name' => $this->setting('biteship_origin_contact_name', config('services.biteship.origin_contact_name')),
            'origin_contact_phone' => $this->setting('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone')),
            'origin_area_id' => $this->setting('biteship_origin_area_id', config('services.biteship.origin_area_id')),
            'origin_address' => $this->setting('biteship_origin_address', config('services.biteship.origin_address')),
            'origin_postal_code' => $this->setting('biteship_origin_postal_code', config('services.biteship.origin_postal_code')),
            'destination_contact_name' => $destination['contact_name'],
            'destination_contact_phone' => $destination['contact_phone'],
            'destination_area_id' => $destination['area_id'] ?? null,
            'destination_address' => $destination['address'],
            'destination_postal_code' => $destination['postal_code'],
            'couriers' => implode(',', $couriers),
            'items' => $items,
        ];

        $this->addCoordinates($payload, 'origin', $this->setting('biteship_origin_latitude', config('services.biteship.origin_latitude')), $this->setting('biteship_origin_longitude', config('services.biteship.origin_longitude')));
        $this->addCoordinates($payload, 'destination', $destination['latitude'] ?? null, $destination['longitude'] ?? null);

        $response = $this->client()->post(self::BASE_URL . '/rates/couriers', array_filter($payload, fn ($value) => filled($value)));

        if ($response->failed()) {
            throw new \RuntimeException($response->json('error') ?: $response->json('message') ?: 'Gagal mengambil ongkir Biteship.');
        }

        return $response->json('pricing', $response->json('data', [])) ?: [];
    }

    public function createOrder(Order $order): array
    {
        $order->loadMissing(['user', 'address', 'items.product', 'shipment']);

        if (!$order->shipment) {
            throw new \RuntimeException('Data shipment belum tersedia.');
        }

        $address = $order->address;

        if (!$address) {
            throw new \RuntimeException('Alamat order tidak ditemukan.');
        }

        $payload = [
            'shipper_contact_name' => $this->setting('biteship_origin_contact_name', config('services.biteship.origin_contact_name')),
            'shipper_contact_phone' => $this->setting('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone')),
            'origin_contact_name' => $this->setting('biteship_origin_contact_name', config('services.biteship.origin_contact_name')),
            'origin_contact_phone' => $this->setting('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone')),
            'origin_area_id' => $this->setting('biteship_origin_area_id', config('services.biteship.origin_area_id')),
            'origin_address' => $this->setting('biteship_origin_address', config('services.biteship.origin_address')),
            'origin_postal_code' => $this->setting('biteship_origin_postal_code', config('services.biteship.origin_postal_code')),
            'destination_contact_name' => $address->receiver_name,
            'destination_contact_phone' => $address->phone,
            'destination_area_id' => $address->biteship_area_id,
            'destination_address' => trim($address->address . ', ' . $address->subdistrict . ', ' . $address->district . ', ' . $address->city . ', ' . $address->province),
            'destination_postal_code' => $address->postal_code,
            'courier_company' => $order->shipment->courier,
            'courier_type' => $order->shipment->service_code ?: $order->shipment->service,
            'delivery_type' => 'now',
            'order_note' => $order->notes,
            'items' => $order->items->map(function ($item) {
                $weight = max(1, (int) ceil((float) ($item->variant?->weight ?: $item->product?->weight ?: 300)));
                return [
                    'name' => $item->product_name,
                    'description' => $item->variant_name ?: $item->product_name,
                    'value' => (int) $item->price,
                    'quantity' => (int) $item->qty,
                    'weight' => $weight,
                    'length' => 20,
                    'width' => 20,
                    'height' => 5,
                ];
            })->values()->all(),
        ];

        $response = $this->client()->post(self::BASE_URL . '/orders', array_filter($payload, fn ($value) => filled($value)));

        if ($response->failed()) {
            $errMsg = $response->json('error')
                ?: $response->json('message')
                ?: $response->json('error_message')
                ?: $response->json('errors.0.message')
                ?: ('Gagal membuat order Biteship. HTTP ' . $response->status());
            throw new \RuntimeException($errMsg);
        }

        return $response->json();
    }

    public function searchAreas(string $input, ?string $apiKey = null): array
    {
        $response = $this->client($apiKey)->get(self::BASE_URL . '/maps/areas', [
            'countries' => 'ID',
            'input' => $input,
            'type' => 'single',
        ]);

        if ($response->failed()) {
            throw new \RuntimeException($response->json('error') ?: $response->json('message') ?: 'Gagal mencari area Biteship.');
        }

        $areas = $response->json('areas') ?? $response->json('data') ?? $response->json();

        if (isset($areas['areas'])) {
            $areas = $areas['areas'];
        }

        if (isset($areas['data'])) {
            $areas = $areas['data'];
        }

        if (isset($areas['id']) || isset($areas['area_id'])) {
            $areas = [$areas];
        }

        return collect($areas)->map(function ($area) {
            if (!is_array($area)) {
                return null;
            }

            $postalCode = $area['postal_code'] ?? $area['zip_code'] ?? ($area['postal_codes'][0] ?? null);
            $province = $area['administrative_division_level_1_name'] ?? $area['province_name'] ?? $area['province'] ?? null;
            $city = $area['administrative_division_level_2_name'] ?? $area['city_name'] ?? $area['city'] ?? null;
            $district = $area['administrative_division_level_3_name'] ?? $area['district_name'] ?? $area['district'] ?? null;
            $subdistrict = $area['administrative_division_level_4_name'] ?? $area['subdistrict_name'] ?? $area['village_name'] ?? null;

            return [
                'id' => (string) ($area['id'] ?? $area['area_id'] ?? ''),
                'label' => $this->areaLabel([$subdistrict, $district, $city, $province, $postalCode]),
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'subdistrict' => $subdistrict,
                'postal_code' => $postalCode,
                'latitude' => $area['latitude'] ?? $area['lat'] ?? null,
                'longitude' => $area['longitude'] ?? $area['lng'] ?? $area['long'] ?? null,
            ];
        })->filter(fn ($area) => is_array($area) && filled($area['id']) && filled($area['label']))->values()->all();
    }

    public function track(string $waybill, string $courier): ?array
    {
        $attempts = [
            fn () => $this->client()->get(self::BASE_URL . '/trackings/' . urlencode($waybill)),
            fn () => $this->client()->get(self::BASE_URL . '/trackings/' . urlencode($waybill) . '/couriers/' . urlencode($courier)),
            fn () => $this->client()->get(self::BASE_URL . '/trackings', [
                'waybill_id' => $waybill,
                'courier_code' => $courier,
            ]),
            fn () => $this->client()->post(self::BASE_URL . '/trackings', [
                'waybill_id' => $waybill,
                'courier_code' => $courier,
            ]),
        ];

        $lastError = null;

        foreach ($attempts as $request) {
            try {
                $response = $request();

                if ($response->successful()) {
                    return $this->normalizeTracking($response->json(), $waybill, $courier);
                }

                $error = $response->json('error')
                    ?: $response->json('message')
                    ?: $response->body();

                $lastError = $this->preferredTrackingError($lastError, $error);
            } catch (Throwable $e) {
                $lastError = $this->preferredTrackingError($lastError, $e->getMessage());
            }
        }

        throw new \RuntimeException($lastError ?: 'Tracking Biteship gagal diproses.');
    }

    public function normalizeRates(array $pricing): array
    {
        return collect($pricing)->map(function ($rate) {
            $courierCode = $rate['courier_code'] ?? $rate['courier_company'] ?? $rate['company'] ?? null;
            $serviceCode = $rate['courier_service_code'] ?? $rate['courier_type'] ?? $rate['type'] ?? null;

            return [
                'code' => $courierCode,
                'service' => $serviceCode,
                'name' => $rate['courier_name'] ?? strtoupper((string) $courierCode),
                'description' => $rate['description'] ?? ($rate['courier_service_name'] ?? $serviceCode),
                'cost' => (int) ($rate['price'] ?? $rate['total_price'] ?? 0),
                'etd' => $rate['duration'] ?? $rate['shipment_duration_range'] ?? '-',
            ];
        })->filter(fn ($rate) => filled($rate['code']) && filled($rate['service']) && $rate['cost'] >= 0)->values()->all();
    }

    private function client(?string $apiKey = null)
    {
        $apiKey = $apiKey ?: $this->setting('biteship_api_key', config('services.biteship.api_key'));

        if (!$apiKey) {
            throw new \RuntimeException('API key Biteship belum diisi.');
        }

        return Http::withToken($apiKey)->acceptJson()->asJson()->timeout(20);
    }

    private function areaLabel(array $parts): string
    {
        return collect($parts)->filter(fn ($part) => filled($part))->unique()->implode(', ');
    }

    private function normalizeTracking(array $payload, string $waybill, string $courier): array
    {
        $data = $payload['data'] ?? $payload;
        $summary = $data['summary'] ?? [];
        $history = $data['manifest']
            ?? $data['history']
            ?? $data['histories']
            ?? $data['tracking_history']
            ?? $data['courier']['history']
            ?? [];

        if (isset($history['status']) || isset($history['description'])) {
            $history = [$history];
        }

        return [
            'summary' => [
                'waybill' => $summary['waybill'] ?? $summary['waybill_id'] ?? $data['waybill_id'] ?? $data['waybill_number'] ?? $waybill,
                'courier' => $summary['courier'] ?? $summary['courier_code'] ?? $data['courier_code'] ?? $courier,
                'status' => $this->translateTrackingStatus($summary['status'] ?? $data['status'] ?? $data['tracking_status'] ?? null),
            ],
            'manifest' => collect($history)->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                $dateTime = $item['datetime'] ?? $item['created_at'] ?? $item['updated_at'] ?? null;
                $description = $item['manifest_description'] ?? $item['description'] ?? $item['note'] ?? $item['message'] ?? $item['status'] ?? '-';

                return [
                    'manifest_description' => $this->translateTrackingDescription($description, $item['status'] ?? null),
                    'city_name' => $item['city_name'] ?? $item['location'] ?? $item['area_name'] ?? '',
                    'manifest_date' => $item['manifest_date'] ?? $item['date'] ?? ($dateTime ? substr((string) $dateTime, 0, 10) : ''),
                    'manifest_time' => $item['manifest_time'] ?? $item['time'] ?? ($dateTime ? substr((string) $dateTime, 11, 8) : ''),
                ];
            })->filter()->values()->all(),
            'raw' => $payload,
        ];
    }

    private function preferredTrackingError(?string $current, ?string $next): ?string
    {
        if (!$current) {
            return $next;
        }

        if ($next && !str_contains(strtolower($next), 'route not found')) {
            return $next;
        }

        return $current;
    }

    private function translateTrackingStatus(?string $status): ?string
    {
        if (blank($status)) {
            return $status;
        }

        return match (strtolower(trim((string) $status))) {
            'confirmed' => 'Dikonfirmasi',
            'allocated' => 'Kurir dialokasikan',
            'picking_up', 'pickup', 'picked_up' => 'Sedang dijemput',
            'dropping_off', 'in_transit', 'on_delivery' => 'Dalam perjalanan',
            'delivered' => 'Terkirim',
            'cancelled', 'canceled' => 'Dibatalkan',
            'returned' => 'Dikembalikan',
            'failed' => 'Gagal dikirim',
            default => ucfirst(str_replace('_', ' ', (string) $status)),
        };
    }

    private function translateTrackingDescription(string $description, ?string $status = null): string
    {
        $lower = strtolower($description);
        $statusLabel = $this->translateTrackingStatus($status);

        if (str_contains($lower, 'courier order is confirmed')) {
            return 'Pesanan pengiriman sudah dikonfirmasi. Kurir telah diberitahu untuk melakukan penjemputan.'
                . $this->pickupNumberSuffix($description);
        }

        if (str_contains($lower, 'has been notified to pick up')) {
            return 'Kurir telah diberitahu untuk menjemput paket.' . $this->pickupNumberSuffix($description);
        }

        if (str_contains($lower, 'allocated')) {
            return 'Kurir sudah dialokasikan untuk pesanan ini.';
        }

        if (str_contains($lower, 'picked up') || str_contains($lower, 'pickup')) {
            return 'Paket sudah dijemput oleh kurir.';
        }

        if (str_contains($lower, 'delivered')) {
            return 'Paket sudah diterima oleh penerima.';
        }

        if (str_contains($lower, 'cancel')) {
            return 'Pengiriman dibatalkan.';
        }

        if ($statusLabel && $description === (string) $status) {
            return $statusLabel;
        }

        return $description;
    }

    private function pickupNumberSuffix(string $description): string
    {
        if (preg_match('/Pickup Number:\s*([A-Za-z0-9-]+)/i', $description, $matches)) {
            return ' Nomor pickup: ' . $matches[1] . '.';
        }

        return '';
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }

    private function addCoordinates(array &$payload, string $prefix, mixed $latitude, mixed $longitude): void
    {
        if (filled($latitude) && filled($longitude)) {
            $payload[$prefix . '_latitude'] = (float) $latitude;
            $payload[$prefix . '_longitude'] = (float) $longitude;
        }
    }
}
