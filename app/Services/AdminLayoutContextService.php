<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AdminLayoutContextService
{
    public function layoutData(): array
    {
        $storeLogo = Setting::getValue('store_logo');
        $notifications = $this->notifications();

        return [
            'adminStoreName' => Setting::getValue('store_name', 'FURE'),
            'adminStoreLogo' => $storeLogo,
            'adminUser' => Auth::user(),
            'adminPendingOrderCount' => $notifications['pending_order_count'],
            'adminHasNotif' => $notifications['total'] > 0,
            'adminNotifPendingOrders' => $notifications['pending_orders'],
            'adminNotifLowStock' => $notifications['low_stock'],
            'adminNotifNewReviews' => $notifications['new_reviews'],
            'adminNotifTotal' => $notifications['total'],
        ];
    }

    private function notifications(): array
    {
        return Cache::remember('admin.layout.notifications', now()->addMinute(), function () {
            $pendingOrders = Order::with('user:id,name,email')
                ->where('status', 'pending')
                ->latest()
                ->take(3)
                ->get();
            $lowStock = Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->take(3)
                ->get(['id', 'name', 'stock']);
            $newReviews = Review::with(['product:id,name', 'user:id,name'])
                ->where('is_verified', false)
                ->latest()
                ->take(3)
                ->get();
            $pendingOrderCount = Order::where('status', 'pending')->count();

            return [
                'pending_order_count' => $pendingOrderCount,
                'pending_orders' => $pendingOrders,
                'low_stock' => $lowStock,
                'new_reviews' => $newReviews,
                'total' => $pendingOrders->count() + $lowStock->count() + $newReviews->count(),
            ];
        });
    }
}
