<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Collection;
use App\Models\Setting;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class StorefrontContextService
{
    public function layoutData(): array
    {
        $store = $this->store();
        $customer = $this->customerContext();

        return [
            'store' => $store,
            'storeName' => $store['name'],
            'storeLogo' => $store['logo'],
            'storeEmail' => $store['email'],
            'storePhone' => $store['phone'],
            'storeAddress' => $store['address'],
            'storeInstagram' => $store['instagram'],
            'storeTiktok' => $store['tiktok'],
            'storeWhatsapp' => $store['whatsapp'],
            'navCollections' => Collection::where('is_active', true)
                ->where('show_in_nav', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'seoDefaults' => [
                'title' => $store['name'] . ' — Hijab Premium & Modest Wear',
                'description' => 'Belanja koleksi hijab premium dan modest wear ' . $store['name'] . ' dengan bahan nyaman, warna lembut, dan desain elegan. Tersedia hijab syari, hijab daily, new arrival, dan best seller. Pengiriman ke seluruh Indonesia.',
                'keywords' => $store['name'] . ', ' . strtolower($store['name']) . ' hijab, hijab premium, hijab syari, hijab daily, hijab wanita, modest wear, toko hijab online',
                'image' => $store['logo'] ? asset('storage/' . $store['logo']) : asset('favicon.ico'),
            ],
            'customerNav' => $customer,
            'currentUser' => $customer['user'],
            'isCustomer' => $customer['is_customer'],
            'wishlistCount' => $customer['wishlist_count'],
            'cartCount' => $customer['cart_count'],
        ];
    }

    public function store(): array
    {
        return [
            'name' => Setting::getValue('store_name', 'FURE'),
            'logo' => Setting::getValue('store_logo'),
            'email' => Setting::getValue('store_email'),
            'phone' => Setting::getValue('store_phone'),
            'address' => Setting::getValue('store_address'),
            'instagram' => Setting::getValue('store_instagram'),
            'tiktok' => Setting::getValue('store_tiktok'),
            'whatsapp' => Setting::getValue('store_whatsapp'),
            'origin_phone' => Setting::getValue('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone', '081297536686')),
        ];
    }

    private function customerContext(): array
    {
        $user = Auth::user();
        $isCustomer = $user?->role === 'customer';
        $wishlistCount = 0;
        $cartCount = 0;

        if ($isCustomer) {
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->withCount('items')
                ->first(['id', 'user_id']);
            $cartCount = (int) ($cart->items_count ?? 0);
        }

        return [
            'user' => $user,
            'is_customer' => $isCustomer,
            'wishlist_count' => $wishlistCount,
            'cart_count' => $cartCount,
        ];
    }
}
