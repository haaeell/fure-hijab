<?php

namespace App\Services;

use Illuminate\Support\Collection;

class LandingPageViewDataService
{
    public function __construct(private StorefrontContextService $storefrontContext)
    {
    }

    public function homeData(Collection $landingBanners, Collection $landingSections, Collection $shopLookProducts): array
    {
        $store = $this->storefrontContext->store();
        $fallbackHero = 'banner2.webp';
        $fallbackEditorial = 'banner2.webp';
        $defaultBanners = collect([
            (object) [
                'eyebrow' => 'New Collection',
                'title' => 'FURE',
                'subtitle' => 'Koleksi hijab dan modest wear bernuansa lembut, clean, dan siap dipakai dari hari biasa sampai momen spesial.',
                'image' => null,
                'mobile_image' => null,
                'primary_button_text' => 'Belanja Sekarang',
                'primary_button_url' => route('new-arrived.index'),
                'secondary_button_text' => 'Lihat Koleksi',
                'secondary_button_url' => route('collections.index'),
            ],
        ]);
        $heroBanners = $landingBanners->count() > 0 ? $landingBanners : $defaultBanners;
        $defaultSections = collect([
            (object) [
                'eyebrow' => 'Best Seller',
                'title' => 'Hijab Favorit Minggu Ini',
                'subtitle' => 'Pilihan yang paling sering dibeli untuk daily look.',
                'button_text' => 'Shop Now',
                'button_url' => route('best-seller.index'),
                'image' => null,
                'icon' => 'fa-solid fa-star',
                'background_color' => '#d8c8b8',
                'text_color' => '#5F4A3A',
            ],
            (object) [
                'eyebrow' => 'Special Promo',
                'title' => 'Voucher Hijab Deals',
                'subtitle' => 'Promo eksklusif untuk koleksi pilihan.',
                'button_text' => 'Claim Promo',
                'button_url' => route('promo.index'),
                'image' => null,
                'icon' => 'fa-solid fa-tags',
                'background_color' => '#5F4A3A',
                'text_color' => '#ffffff',
            ],
            (object) [
                'eyebrow' => 'New Arrival',
                'title' => 'Fresh Drop Setiap Pekan',
                'subtitle' => 'Model terbaru untuk melengkapi wardrobe modest kamu.',
                'button_text' => 'Explore',
                'button_url' => route('new-arrived.index'),
                'image' => null,
                'icon' => 'fa-solid fa-bag-shopping',
                'background_color' => '#eee5dc',
                'text_color' => '#5F4A3A',
            ],
        ]);
        $promoSections = $landingSections->count() > 0 ? $landingSections : $defaultSections;
        $shopLookHero = $shopLookProducts->first();
        $shopLookImage = $shopLookHero && $shopLookHero->images->first()
            ? asset('storage/' . $shopLookHero->images->first()->image_url)
            : $fallbackEditorial;
        $featureBanners = collect([
            (object) [
                'eyebrow' => 'FURE Signature',
                'title' => 'Clean Layers for Every Day',
                'subtitle' => 'Hijab premium dengan warna lembut, mudah dipadukan, dan nyaman dipakai sepanjang hari.',
                'button_text' => 'Explore Collection',
                'button_url' => route('collections.index'),
                'image' => '/banner3.webp',
                'align' => 'left',
            ],
            (object) [
                'eyebrow' => 'Daily Essential',
                'title' => 'Soft Colors, Effortless Fit',
                'subtitle' => 'Temukan pilihan bahan ringan untuk aktivitas harian sampai momen spesial.',
                'button_text' => 'Shop New Arrival',
                'button_url' => route('new-arrived.index'),
                'image' => '/banner2.webp',
                'align' => 'right',
            ],
            (object) [
                'eyebrow' => 'Limited Deals',
                'title' => 'Special Picks This Week',
                'subtitle' => 'Koleksi favorit dengan penawaran khusus untuk tampilan modest yang rapi.',
                'button_text' => 'Claim Promo',
                'button_url' => route('promo.index'),
                'image' => '/banner2.webp',
                'align' => 'left',
            ],
        ]);
        $homeSeoImage = $heroBanners->first()?->image
            ? asset('storage/' . $heroBanners->first()->image)
            : $fallbackHero;

        return [
            'fallbackHero' => $fallbackHero,
            'fallbackEditorial' => $fallbackEditorial,
            'heroBanners' => $heroBanners,
            'promoSections' => $promoSections,
            'shopLookImage' => $shopLookImage,
            'featureBanners' => $featureBanners,
            'homeSeoImage' => $homeSeoImage,
            'chatUrl' => $this->chatUrl($store),
            'organizationSchema' => $this->organizationSchema($store),
            'websiteSchema' => $this->websiteSchema($store),
            'localBusinessSchema' => $this->localBusinessSchema($store),
        ];
    }

    private function chatUrl(array $store): string
    {
        $phone = preg_replace('/\D+/', '', (string) ($store['whatsapp'] ?: $store['origin_phone']));
        $phone = str_starts_with($phone, '0') ? '62' . substr($phone, 1) : $phone;
        $message = rawurlencode('Halo ' . $store['name'] . ', saya mau tanya koleksi dan pesanan.');

        return $phone ? "https://api.whatsapp.com/send?phone={$phone}&text={$message}" : '#';
    }

    private function organizationSchema(array $store): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $store['name'],
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $store['logo'] ? asset('storage/' . $store['logo']) : asset('favicon.ico'),
            ],
            'sameAs' => array_values(array_filter([$store['instagram'], $store['tiktok']])),
        ];

        $phone = $store['phone'] ?: $store['whatsapp'];
        if ($phone) {
            $schema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $phone,
                'contactType' => 'customer service',
                'areaServed' => 'ID',
                'availableLanguage' => ['Indonesian'],
            ];
        }

        return $schema;
    }

    private function websiteSchema(array $store): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $store['name'],
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('collections.index') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private function localBusinessSchema(array $store): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ClothingStore',
            'name' => $store['name'],
            'url' => url('/'),
            'image' => $store['logo'] ? asset('storage/' . $store['logo']) : asset('favicon.ico'),
            'description' => $store['name'] . ' adalah toko hijab premium online yang menyediakan koleksi hijab syari, hijab daily, dan modest wear dengan bahan berkualitas.',
            'priceRange' => '$$',
            'currenciesAccepted' => 'IDR',
            'paymentAccepted' => 'Credit Card, Bank Transfer, GoPay, OVO, QRIS',
            'areaServed' => 'ID',
        ];

        if ($store['address']) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $store['address'],
                'addressCountry' => 'ID',
            ];
        }

        if ($store['phone'] ?: $store['whatsapp']) {
            $schema['telephone'] = $store['phone'] ?: $store['whatsapp'];
        }

        if ($store['email']) {
            $schema['email'] = $store['email'];
        }

        $sameAs = array_values(array_filter([$store['instagram'], $store['tiktok']]));
        if ($sameAs) {
            $schema['sameAs'] = $sameAs;
        }

        return $schema;
    }
}
