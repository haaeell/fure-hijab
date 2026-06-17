<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\LandingBanner;
use App\Models\LandingSection;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@mail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        /*
        |--------------------------------------------------------------------------
        | COUPONS
        |--------------------------------------------------------------------------
        */
        Coupon::create([
            'code' => 'DISKON10',
            'name' => 'Diskon 10%',
            'type' => 'percent',
            'value' => 10,
            'min_purchase' => 50000,
            'max_discount' => 20000,
            'quota' => 100,
        ]);

        Coupon::create([
            'code' => 'HEMAT5000',
            'name' => 'Potongan 5000',
            'type' => 'fixed',
            'value' => 5000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SETTINGS
        |--------------------------------------------------------------------------
        */
        $settings = [
            'store_name'                     => 'FURE',
            'store_email'                    => 'hello@fure.id',
            'store_phone'                    => '02112345678',
            'store_whatsapp'                 => '6281234567890',
            'store_address'                  => 'Jl. Contoh No. 1, Jakarta Selatan, DKI Jakarta 12345',
            'store_instagram'                => '@fure.id',
            'store_tiktok'                   => '@fure.id',
            'biteship_api_key'               => '',
            'biteship_webhook_secret'        => '',
            'biteship_origin_area_id'        => '',
            'biteship_origin_area_label'     => '',
            'biteship_origin_contact_name'   => 'Admin FURE',
            'biteship_origin_contact_phone'  => '6281234567890',
            'biteship_origin_address'        => 'Jl. Contoh No. 1, Jakarta Selatan',
            'biteship_origin_postal_code'    => '12345',
            'midtrans_server_key'            => '',
            'midtrans_client_key'            => '',
            'midtrans_is_production'         => '0',
            'midtrans_is_sanitized'          => '1',
            'midtrans_is_3ds'                => '1',
            'mail_mailer'                    => 'log',
            'mail_scheme'                    => 'smtp',
            'mail_host'                      => 'smtp.mailtrap.io',
            'mail_port'                      => '2525',
            'mail_username'                  => '',
            'mail_password'                  => '',
            'mail_from_address'              => 'hello@fure.id',
            'mail_from_name'                 => 'FURE',
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        /*
        |--------------------------------------------------------------------------
        | LANDING BANNERS
        |--------------------------------------------------------------------------
        */
        LandingBanner::create([
            'eyebrow'              => 'Koleksi Terbaru',
            'title'                => 'Tampil Anggun dengan Hijab Premium',
            'subtitle'             => 'Temukan koleksi hijab premium kami yang elegan dan nyaman dipakai sehari-hari.',
            'image'                => null,
            'mobile_image'         => null,
            'primary_button_text'  => 'Belanja Sekarang',
            'primary_button_url'   => '/collections',
            'secondary_button_text'=> 'Lihat Koleksi',
            'secondary_button_url' => '/collections',
            'sort_order'           => 0,
            'is_active'            => true,
        ]);

        LandingBanner::create([
            'eyebrow'              => 'Promo Spesial',
            'title'                => 'Diskon hingga 30% untuk Koleksi Pashmina',
            'subtitle'             => 'Penawaran terbatas, segera dapatkan koleksi favoritmu sebelum kehabisan.',
            'image'                => null,
            'mobile_image'         => null,
            'primary_button_text'  => 'Klaim Diskon',
            'primary_button_url'   => '/collections/pashmina',
            'secondary_button_text'=> null,
            'secondary_button_url' => null,
            'sort_order'           => 1,
            'is_active'            => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | LANDING SECTIONS
        |--------------------------------------------------------------------------
        */
        LandingSection::create([
            'eyebrow'          => 'Keunggulan Kami',
            'title'            => 'Kualitas Terjamin, Harga Terjangkau',
            'subtitle'         => 'Setiap produk kami dipilih dengan cermat untuk memastikan kenyamanan dan kualitas terbaik.',
            'button_text'      => 'Tentang Kami',
            'button_url'       => '/about',
            'image'            => null,
            'icon'             => null,
            'background_color' => '#eee5dc',
            'text_color'       => '#5F4A3A',
            'sort_order'       => 0,
            'is_active'        => true,
        ]);

        LandingSection::create([
            'eyebrow'          => 'Pengiriman Cepat',
            'title'            => 'Dikirim ke Seluruh Indonesia',
            'subtitle'         => 'Kami bekerja sama dengan kurir terpercaya untuk memastikan paket sampai tepat waktu.',
            'button_text'      => null,
            'button_url'       => null,
            'image'            => null,
            'icon'             => null,
            'background_color' => '#f5f0eb',
            'text_color'       => '#3A2E28',
            'sort_order'       => 1,
            'is_active'        => true,
        ]);
    }
}
