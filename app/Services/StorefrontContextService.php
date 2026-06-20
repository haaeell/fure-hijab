<?php

namespace App\Services;

use App\Models\Cart;
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
            'seoDefaults' => [
                'title' => 'Hijab Premium dan Modest Wear',
                'description' => 'Belanja koleksi hijab premium dan modest wear FURE dengan bahan nyaman, warna lembut, dan desain elegan untuk aktivitas harian hingga momen spesial.',
                'keywords' => 'hijab premium, hijab wanita, modest wear, hijab elegan, hijab terbaru, FURE',
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
