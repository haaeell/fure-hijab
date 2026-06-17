<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\BiteshipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AdminSettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'settings' => $this->settings(),
        ]);
    }

    public function storeIndex(): View
    {
        return view('settings.store', [
            'settings' => [
                'store_name'      => Setting::getValue('store_name', config('app.name', 'FURE')),
                'store_email'     => Setting::getValue('store_email', config('mail.from.address')),
                'store_phone'     => Setting::getValue('store_phone'),
                'store_whatsapp'  => Setting::getValue('store_whatsapp'),
                'store_address'   => Setting::getValue('store_address'),
                'store_instagram' => Setting::getValue('store_instagram'),
                'store_tiktok'    => Setting::getValue('store_tiktok'),
                'store_logo'      => Setting::getValue('store_logo'),
            ],
        ]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name'      => ['required', 'string', 'max:120'],
            'store_email'     => ['nullable', 'email', 'max:255'],
            'store_phone'     => ['nullable', 'string', 'max:30'],
            'store_whatsapp'  => ['nullable', 'string', 'max:30'],
            'store_address'   => ['nullable', 'string', 'max:1000'],
            'store_instagram' => ['nullable', 'string', 'max:255'],
            'store_tiktok'    => ['nullable', 'string', 'max:255'],
            'store_logo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('store_logo')) {
            $oldLogo = Setting::getValue('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['store_logo'] = $request->file('store_logo')->store('settings', 'public');
        } else {
            unset($validated['store_logo']);
        }

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value ?? '');
        }

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'store_email' => ['required', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:30'],
            'store_whatsapp' => ['nullable', 'string', 'max:30'],
            'store_address' => ['nullable', 'string', 'max:1000'],
            'store_instagram' => ['nullable', 'string', 'max:255'],
            'store_tiktok' => ['nullable', 'string', 'max:255'],
            'store_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'biteship_api_key' => ['required', 'string'],
            'biteship_webhook_secret' => ['nullable', 'string', 'max:255'],
            'biteship_origin_area_id' => ['nullable', 'string', 'max:255'],
            'biteship_origin_area_label' => ['nullable', 'string', 'max:500'],
            'biteship_origin_contact_name' => ['required', 'string', 'max:255'],
            'biteship_origin_contact_phone' => ['required', 'string', 'max:30'],
            'biteship_origin_address' => ['required', 'string'],
            'biteship_origin_postal_code' => ['required', 'string', 'max:10'],
            'biteship_origin_latitude' => ['nullable', 'numeric'],
            'biteship_origin_longitude' => ['nullable', 'numeric'],
            'midtrans_server_key' => ['required', 'string'],
            'midtrans_client_key' => ['required', 'string'],
            'midtrans_is_production' => ['nullable', 'boolean'],
            'midtrans_is_sanitized' => ['nullable', 'boolean'],
            'midtrans_is_3ds' => ['nullable', 'boolean'],
            'mail_mailer' => ['required', 'in:smtp,log,array'],
            'mail_scheme' => ['required_if:mail_mailer,smtp', 'nullable', 'in:smtp,smtps'],
            'mail_host' => ['required_if:mail_mailer,smtp', 'nullable', 'string', 'max:255'],
            'mail_port' => ['required_if:mail_mailer,smtp', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        if ($request->hasFile('store_logo')) {
            $oldLogo = Setting::getValue('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['store_logo'] = $request->file('store_logo')->store('settings', 'public');
        }

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

    public function searchBiteshipAreas(Request $request, BiteshipService $biteship): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:3'],
            'api_key' => ['nullable', 'string'],
        ]);

        try {
            return response()->json($biteship->searchAreas($validated['search'], $validated['api_key'] ?? null));
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 422);
        }
    }

    public function testEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $this->applyMailSettings($this->settings());

        try {
            Mail::raw('Email percobaan dari FURE. Jika email ini masuk, konfigurasi SMTP sudah aktif.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('Test Email FURE');
            });
        } catch (Throwable $th) {
            return back()->with('error', 'Gagal mengirim test email: ' . $th->getMessage());
        }

        return back()->with('success', 'Test email berhasil dikirim ke ' . $validated['test_email'] . '.');
    }

    private function settings(): array
    {
        return [
            'biteship_api_key' => Setting::getValue('biteship_api_key', config('services.biteship.api_key')),
            'store_name' => Setting::getValue('store_name', config('app.name', 'FURE')),
            'store_email' => Setting::getValue('store_email', config('mail.from.address')),
            'store_phone' => Setting::getValue('store_phone'),
            'store_whatsapp' => Setting::getValue('store_whatsapp', Setting::getValue('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone'))),
            'store_address' => Setting::getValue('store_address'),
            'store_instagram' => Setting::getValue('store_instagram'),
            'store_tiktok' => Setting::getValue('store_tiktok'),
            'store_logo' => Setting::getValue('store_logo'),
            'biteship_webhook_secret' => Setting::getValue('biteship_webhook_secret', config('services.biteship.webhook_secret')),
            'biteship_origin_area_id' => Setting::getValue('biteship_origin_area_id', config('services.biteship.origin_area_id')),
            'biteship_origin_area_label' => Setting::getValue('biteship_origin_area_label'),
            'biteship_origin_contact_name' => Setting::getValue('biteship_origin_contact_name', config('services.biteship.origin_contact_name')),
            'biteship_origin_contact_phone' => Setting::getValue('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone')),
            'biteship_origin_address' => Setting::getValue('biteship_origin_address', config('services.biteship.origin_address')),
            'biteship_origin_postal_code' => Setting::getValue('biteship_origin_postal_code', config('services.biteship.origin_postal_code')),
            'biteship_origin_latitude' => Setting::getValue('biteship_origin_latitude', config('services.biteship.origin_latitude')),
            'biteship_origin_longitude' => Setting::getValue('biteship_origin_longitude', config('services.biteship.origin_longitude')),
            'midtrans_server_key' => Setting::getValue('midtrans_server_key', config('services.midtrans.server_key')),
            'midtrans_client_key' => Setting::getValue('midtrans_client_key', config('services.midtrans.client_key')),
            'midtrans_is_production' => Setting::getValue('midtrans_is_production', config('services.midtrans.is_production')),
            'midtrans_is_sanitized' => Setting::getValue('midtrans_is_sanitized', config('services.midtrans.is_sanitized')),
            'midtrans_is_3ds' => Setting::getValue('midtrans_is_3ds', config('services.midtrans.is_3ds')),
            'mail_mailer' => Setting::getValue('mail_mailer', config('mail.default')),
            'mail_scheme' => Setting::getValue('mail_scheme', config('mail.mailers.smtp.scheme')),
            'mail_host' => Setting::getValue('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => Setting::getValue('mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => Setting::getValue('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => Setting::getValue('mail_password', config('mail.mailers.smtp.password')),
            'mail_from_address' => Setting::getValue('mail_from_address', config('mail.from.address')),
            'mail_from_name' => Setting::getValue('mail_from_name', config('mail.from.name')),
        ];
    }

    private function applyMailSettings(array $settings): void
    {
        config([
            'mail.default' => $settings['mail_mailer'],
            'mail.mailers.smtp.scheme' => $settings['mail_scheme'],
            'mail.mailers.smtp.host' => $settings['mail_host'],
            'mail.mailers.smtp.port' => (int) $settings['mail_port'],
            'mail.mailers.smtp.username' => $settings['mail_username'],
            'mail.mailers.smtp.password' => $settings['mail_password'],
            'mail.from.address' => $settings['mail_from_address'],
            'mail.from.name' => $settings['mail_from_name'],
        ]);
    }
}
