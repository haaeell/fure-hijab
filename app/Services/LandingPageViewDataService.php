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
        $fallbackHero = 'https://images.unsplash.com/photo-1585435465945-bef5a93f8849?auto=format&fit=crop&q=85&w=1800';
        $fallbackEditorial = 'https://images.unsplash.com/photo-1618232118117-98d49b20e2f5?auto=format&fit=crop&q=85&w=1400';
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
                'image' => $fallbackEditorial,
                'align' => 'left',
            ],
            (object) [
                'eyebrow' => 'Daily Essential',
                'title' => 'Soft Colors, Effortless Fit',
                'subtitle' => 'Temukan pilihan bahan ringan untuk aktivitas harian sampai momen spesial.',
                'button_text' => 'Shop New Arrival',
                'button_url' => route('new-arrived.index'),
                'image' => $fallbackHero,
                'align' => 'right',
            ],
            (object) [
                'eyebrow' => 'Limited Deals',
                'title' => 'Special Picks This Week',
                'subtitle' => 'Koleksi favorit dengan penawaran khusus untuk tampilan modest yang rapi.',
                'button_text' => 'Claim Promo',
                'button_url' => route('promo.index'),
                'image' => 'https://images.unsplash.com/photo-1594744803329-e58b31de8bf5?auto=format&fit=crop&q=85&w=1600',
                'align' => 'left',
            ],
            (object) [
                'eyebrow' => 'Style Edit',
                'title' => 'One Look, Many Moments',
                'subtitle' => 'Padukan hijab, atasan, dan warna netral untuk tampilan santun yang tetap modern.',
                'button_text' => 'Shop The Look',
                'button_url' => route('collections.index'),
                'image' => $shopLookImage,
                'align' => 'right',
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
        ];
    }

    private function chatUrl(array $store): string
    {
        $phone = preg_replace('/\D+/', '', (string) ($store['whatsapp'] ?: $store['origin_phone']));
        $phone = str_starts_with($phone, '0') ? '62' . substr($phone, 1) : $phone;
        $message = rawurlencode('Halo FURE, saya mau tanya koleksi dan pesanan.');

        return $phone ? "https://api.whatsapp.com/send?phone={$phone}&text={$message}" : '#';
    }

    private function organizationSchema(array $store): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $store['name'],
            'url' => url('/'),
            'logo' => $store['logo'] ? asset('storage/' . $store['logo']) : asset('favicon.ico'),
            'sameAs' => array_values(array_filter([$store['instagram'], $store['tiktok']])),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => $store['phone'] ?: $store['whatsapp'],
                'contactType' => 'customer service',
                'areaServed' => 'ID',
                'availableLanguage' => ['Indonesian'],
            ],
        ];
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
                'target' => route('collections.index') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
}
