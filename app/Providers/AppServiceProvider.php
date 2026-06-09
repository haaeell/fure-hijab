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
                'services.rajaongkir.api_key' => Setting::getValue('rajaongkir_api_key', config('services.rajaongkir.api_key')),
                'services.rajaongkir.origin' => Setting::getValue('rajaongkir_origin', config('services.rajaongkir.origin')),
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
            ]);
        } catch (Throwable $th) {
            // Skip dynamic settings bootstrapping when database is not ready yet.
        }
    }
}
