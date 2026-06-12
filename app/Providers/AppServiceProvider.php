<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            config([
                'services.biteship.api_key' => Setting::getValue('biteship_api_key', config('services.biteship.api_key')),
                'services.biteship.webhook_secret' => Setting::getValue('biteship_webhook_secret', config('services.biteship.webhook_secret')),
                'services.biteship.origin_area_id' => Setting::getValue('biteship_origin_area_id', config('services.biteship.origin_area_id')),
                'services.biteship.origin_contact_name' => Setting::getValue('biteship_origin_contact_name', config('services.biteship.origin_contact_name')),
                'services.biteship.origin_contact_phone' => Setting::getValue('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone')),
                'services.biteship.origin_address' => Setting::getValue('biteship_origin_address', config('services.biteship.origin_address')),
                'services.biteship.origin_postal_code' => Setting::getValue('biteship_origin_postal_code', config('services.biteship.origin_postal_code')),
                'services.biteship.origin_latitude' => Setting::getValue('biteship_origin_latitude', config('services.biteship.origin_latitude')),
                'services.biteship.origin_longitude' => Setting::getValue('biteship_origin_longitude', config('services.biteship.origin_longitude')),
                'services.midtrans.server_key' => Setting::getValue('midtrans_server_key', config('services.midtrans.server_key')),
                'services.midtrans.client_key' => Setting::getValue('midtrans_client_key', config('services.midtrans.client_key')),
                'services.midtrans.is_production' => filter_var(
                    Setting::getValue('midtrans_is_production', config('services.midtrans.is_production')),
                    FILTER_VALIDATE_BOOLEAN
                ),
                'services.midtrans.is_sanitized' => filter_var(
                    Setting::getValue('midtrans_is_sanitized', config('services.midtrans.is_sanitized')),
                    FILTER_VALIDATE_BOOLEAN
                ),
                'services.midtrans.is_3ds' => filter_var(
                    Setting::getValue('midtrans_is_3ds', config('services.midtrans.is_3ds')),
                    FILTER_VALIDATE_BOOLEAN
                ),
                'mail.default' => Setting::getValue('mail_mailer', config('mail.default')),
                'mail.mailers.smtp.scheme' => Setting::getValue('mail_scheme', config('mail.mailers.smtp.scheme')),
                'mail.mailers.smtp.host' => Setting::getValue('mail_host', config('mail.mailers.smtp.host')),
                'mail.mailers.smtp.port' => (int) Setting::getValue('mail_port', config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.username' => Setting::getValue('mail_username', config('mail.mailers.smtp.username')),
                'mail.mailers.smtp.password' => Setting::getValue('mail_password', config('mail.mailers.smtp.password')),
                'mail.from.address' => Setting::getValue('mail_from_address', config('mail.from.address')),
                'mail.from.name' => Setting::getValue('mail_from_name', config('mail.from.name')),
            ]);
        } catch (Throwable $th) {
            // Skip dynamic settings bootstrapping when database is not ready yet.
        }
    }
}
