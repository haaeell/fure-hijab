<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use App\Traits\ExpiresUnpaidOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    use ExpiresUnpaidOrders;

    public function index()
    {
        $this->expireUnpaidOrders();
        $paidStatuses = ['confirmed', 'processing', 'shipped', 'delivered'];

        $totalSales     = Order::whereIn('status', $paidStatuses)->sum('total');
        $totalOrders    = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $avgRating      = Review::avg('rating') ?: 0;

        $thisMonthSales = Order::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        $lastMonthSales = Order::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total');

        $salesGrowth = $lastMonthSales > 0
            ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
            : ($thisMonthSales > 0 ? 100 : 0);

        $salesData = Order::select(
            DB::raw('SUM(total) as sum'),
            DB::raw("DATE_FORMAT(created_at, '%M') as month")
        )
            ->whereIn('status', $paidStatuses)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(created_at)'), 'ASC')
            ->get();

        $recentTransactions = Order::with(['user', 'items.product'])
            ->latest()
            ->take(8)
            ->get();

        $topProducts = Product::with(['images' => fn ($q) => $q->where('is_primary', true)])
            ->orderBy('sold_count', 'desc')
            ->take(5)
            ->get();

        return view('home', compact(
            'totalSales',
            'totalOrders',
            'totalCustomers',
            'avgRating',
            'salesGrowth',
            'thisMonthSales',
            'salesData',
            'recentTransactions',
            'topProducts'
        ));
    }
}
