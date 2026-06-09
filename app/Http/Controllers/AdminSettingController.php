<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'settings' => [
                'rajaongkir_api_key' => Setting::getValue('rajaongkir_api_key', config('services.rajaongkir.api_key')),
                'rajaongkir_origin' => Setting::getValue('rajaongkir_origin', config('services.rajaongkir.origin')),
                'midtrans_server_key' => Setting::getValue('midtrans_server_key', config('services.midtrans.server_key')),
                'midtrans_client_key' => Setting::getValue('midtrans_client_key', config('services.midtrans.client_key')),
                'midtrans_is_production' => Setting::getValue('midtrans_is_production', config('services.midtrans.is_production')),
                'midtrans_is_sanitized' => Setting::getValue('midtrans_is_sanitized', config('services.midtrans.is_sanitized')),
                'midtrans_is_3ds' => Setting::getValue('midtrans_is_3ds', config('services.midtrans.is_3ds')),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rajaongkir_api_key' => ['required', 'string'],
            'rajaongkir_origin' => ['required', 'string', 'max:100'],
            'midtrans_server_key' => ['required', 'string'],
            'midtrans_client_key' => ['required', 'string'],
            'midtrans_is_production' => ['nullable', 'boolean'],
            'midtrans_is_sanitized' => ['nullable', 'boolean'],
            'midtrans_is_3ds' => ['nullable', 'boolean'],
        ]);

        $booleanSettings = [
            'midtrans_is_production',
            'midtrans_is_sanitized',
            'midtrans_is_3ds',
        ];

        foreach ($booleanSettings as $key) {
            $validated[$key] = $request->boolean($key);
        }

        foreach ($validated as $key => $value) {
            Setting::setValue($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        return back()->with('success', 'Pengaturan integrasi berhasil diperbarui.');
    }

    public function searchOrigins(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['required', 'string', 'min:3'],
            'api_key' => ['nullable', 'string'],
        ]);

        $apiKey = $request->input('api_key')
            ?: Setting::getValue('rajaongkir_api_key', config('services.rajaongkir.api_key'));

        if (!$apiKey) {
            return response()->json([
                'message' => 'API key RajaOngkir belum diisi.',
                'data' => [],
            ], 422);
        }

        $response = Http::withHeaders([
            'key' => $apiKey,
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
            'search' => $request->input('search'),
            'limit' => 20,
            'offset' => 0,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal mengambil data origin dari RajaOngkir.',
                'details' => $response->json(),
                'data' => [],
            ], $response->status());
        }

        return response()->json([
            'message' => 'Data origin berhasil diambil.',
            'data' => collect($response->json('data', []))->map(function ($item) {
                $parts = array_filter([
                    $item['subdistrict_name'] ?? null,
                    $item['district_name'] ?? null,
                    $item['city_name'] ?? null,
                    $item['province_name'] ?? null,
                    $item['zip_code'] ?? null,
                ]);

                $label = implode(', ', $parts);

                if ($label === '') {
                    $label = $item['label'] ?? '';
                }

                return [
                    'id' => (string) ($item['id'] ?? ''),
                    'text' => $label,
                ];
            })->filter(fn ($item) => $item['id'] !== '')->values(),
        ]);
    }
}
