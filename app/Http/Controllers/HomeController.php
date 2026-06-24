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
        $now          = Carbon::now();

        // ── All-time KPIs ──────────────────────────────────────────────
        $totalSales     = Order::whereIn('status', $paidStatuses)->sum('total');
        $totalOrders    = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $avgRating      = Review::avg('rating') ?: 0;
        $avgOrderValue  = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // ── This month vs last month ───────────────────────────────────
        $thisMonthSales = Order::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $lastMonthSales = Order::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->sum('total');

        $salesGrowth = $lastMonthSales > 0
            ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
            : ($thisMonthSales > 0 ? 100 : 0);

        $thisMonthOrders = Order::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $lastMonthOrders = Order::whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $ordersGrowth = $lastMonthOrders > 0
            ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100
            : ($thisMonthOrders > 0 ? 100 : 0);

        $newCustomersThisMonth = User::where('role', 'customer')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // ── Operational counters ───────────────────────────────────────
        $pendingOrders    = Order::whereIn('status', ['pending', 'confirmed'])->count();
        $processingOrders = Order::whereIn('status', ['processing', 'shipped'])->count();
        $lowStockCount    = Product::where('stock', '<=', 5)->count();

        // ── Order status distribution ──────────────────────────────────
        $orderStatusData = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── 6-month revenue trend ──────────────────────────────────────
        $salesData = Order::select(
                DB::raw('SUM(total) as sum'),
                DB::raw('COUNT(*) as orders'),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month")
            )
            ->whereIn('status', $paidStatuses)
            ->where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"), DB::raw("DATE_FORMAT(created_at, '%Y%m')"))
            ->orderBy(DB::raw("MIN(created_at)"), 'ASC')
            ->get();

        // ── Top 5 products ─────────────────────────────────────────────
        $topProducts = Product::with(['images' => fn ($q) => $q->where('is_primary', true)])
            ->orderBy('sold_count', 'desc')
            ->take(5)
            ->get();

        $maxSold = $topProducts->max('sold_count') ?: 1;

        // ── Recent 8 orders ────────────────────────────────────────────
        $recentTransactions = Order::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact(
            'totalSales', 'totalOrders', 'totalCustomers', 'avgRating', 'avgOrderValue',
            'thisMonthSales', 'lastMonthSales', 'salesGrowth',
            'thisMonthOrders', 'lastMonthOrders', 'ordersGrowth',
            'newCustomersThisMonth',
            'pendingOrders', 'processingOrders', 'lowStockCount',
            'orderStatusData', 'salesData', 'maxSold',
            'recentTransactions', 'topProducts'
        ));
    }
}
