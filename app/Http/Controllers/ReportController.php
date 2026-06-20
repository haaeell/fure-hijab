<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    private const REVENUE_STATUSES = ['confirmed', 'processing', 'shipped', 'delivered'];

    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $status = $request->get('status');

        $ordersQuery = $this->filteredOrders($startDate, $endDate, $status);
        $revenueQuery = $this->filteredOrders($startDate, $endDate, $status)
            ->whereIn('status', self::REVENUE_STATUSES);

        $summary = [
            'revenue' => (clone $revenueQuery)->sum('total'),
            'orders' => (clone $ordersQuery)->count(),
            'items_sold' => OrderItem::whereHas('order', function (Builder $query) use ($startDate, $endDate, $status) {
                $this->applyOrderFilters($query, $startDate, $endDate, $status)
                    ->whereIn('status', self::REVENUE_STATUSES);
            })->sum('qty'),
            'customers' => (clone $ordersQuery)->distinct('user_id')->count('user_id'),
            'discount' => (clone $ordersQuery)->sum('discount'),
            'shipping' => (clone $ordersQuery)->sum('shipping_cost'),
            'average_order' => (clone $revenueQuery)->avg('total') ?: 0,
        ];

        $statusSummary = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('status')
            ->orderByDesc('total_orders')
            ->get();

        $salesTrend = (clone $revenueQuery)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $topProducts = OrderItem::query()
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->whereHas('order', function (Builder $query) use ($startDate, $endDate, $status) {
                $this->applyOrderFilters($query, $startDate, $endDate, $status)
                    ->whereIn('status', self::REVENUE_STATUSES);
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        $topCustomers = User::query()
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_at')
            )
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('users.role', 'customer')
            ->whereNull('orders.deleted_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->when($status, fn ($query) => $query->where('orders.status', $status))
            ->whereIn('orders.status', self::REVENUE_STATUSES)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $latestOrders = (clone $ordersQuery)
            ->with(['user', 'payment'])
            ->latest()
            ->limit(12)
            ->get();

        $stockAlerts = Product::with(['category', 'brand'])
            ->orderBy('stock')
            ->limit(8)
            ->get();

        $filters = [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => $status,
        ];

        return view('reports.index', compact(
            'summary',
            'statusSummary',
            'salesTrend',
            'topProducts',
            'topCustomers',
            'latestOrders',
            'stockAlerts',
            'filters'
        ));
    }

    public function export(Request $request, string $type): Response|BinaryFileResponse
    {
        [$startDate, $endDate] = $this->resolveExportDateRange($request);
        $status = $request->get('status');
        $format = $request->get('format', 'excel');

        abort_unless(in_array($type, ['orders', 'order-items', 'products', 'customers'], true), 404);
        abort_unless(in_array($format, ['pdf', 'excel'], true), 404);

        $report = $this->buildExportReport($type, $startDate, $endDate, $status);
        $basename = 'laporan-' . $type . '-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd');

        if ($format === 'pdf') {
            return Pdf::loadView('reports.exports.pdf', [
                'title' => $report['title'],
                'headings' => $report['headings'],
                'rows' => $report['rows'],
                'filters' => [
                    'start_date' => $startDate->format('d M Y'),
                    'end_date' => $endDate->format('d M Y'),
                    'status' => $this->statusLabel($status),
                ],
                'generatedAt' => now()->format('d M Y H:i'),
            ])->setPaper('a4', 'landscape')->download($basename . '.pdf');
        }

        return Excel::download(
            new ReportExport($report['title'], $report['headings'], $report['rows']),
            $basename . '.xlsx'
        );
    }

    private function buildExportReport(string $type, Carbon $startDate, Carbon $endDate, ?string $status): array
    {
        return match ($type) {
            'orders' => [
                'title' => 'Laporan Pesanan',
                'headings' => [
                    'Tanggal',
                    'No Order',
                    'Pelanggan',
                    'Email',
                    'Status Order',
                    'Status Bayar',
                    'Metode Bayar',
                    'Subtotal',
                    'Diskon',
                    'Ongkir',
                    'Total',
                    'Kupon',
                ],
                'rows' => $this->orderRows($startDate, $endDate, $status),
            ],
            'order-items' => [
                'title' => 'Laporan Item Pesanan',
                'headings' => [
                    'Tanggal Order',
                    'No Order',
                    'Status Order',
                    'Produk',
                    'Varian',
                    'Qty',
                    'Harga',
                    'Subtotal Item',
                    'Pelanggan',
                ],
                'rows' => $this->orderItemRows($startDate, $endDate, $status),
            ],
            'products' => [
                'title' => 'Laporan Produk',
                'headings' => [
                    'SKU',
                    'Produk',
                    'Kategori',
                    'Brand',
                    'Harga',
                    'Harga Modal',
                    'Stok',
                    'Terjual',
                    'Status',
                ],
                'rows' => $this->productRows(),
            ],
            'customers' => [
                'title' => 'Laporan Pelanggan',
                'headings' => [
                    'Nama',
                    'Email',
                    'Telepon',
                    'Total Order',
                    'Total Belanja',
                    'Order Terakhir',
                    'Tanggal Daftar',
                ],
                'rows' => $this->customerRows($startDate, $endDate, $status),
            ],
        };
    }

    private function orderRows(Carbon $startDate, Carbon $endDate, ?string $status): array
    {
        return $this->filteredOrders($startDate, $endDate, $status)
            ->with(['user', 'payment'])
            ->latest()
            ->get()
            ->map(fn (Order $order) => [
                $order->created_at?->format('Y-m-d H:i:s'),
                $order->order_number,
                $order->user->name ?? '-',
                $order->user->email ?? '-',
                $order->status_label,
                $order->payment->status ?? 'belum_bayar',
                $order->payment->payment_method ?? '-',
                (float) $order->subtotal,
                (float) $order->discount,
                (float) $order->shipping_cost,
                (float) $order->total,
                $order->coupon_code ?? '-',
            ])
            ->all();
    }

    private function orderItemRows(Carbon $startDate, Carbon $endDate, ?string $status): array
    {
        return OrderItem::with(['order.user'])
            ->whereHas('order', fn (Builder $query) => $this->applyOrderFilters($query, $startDate, $endDate, $status))
            ->orderByDesc('id')
            ->get()
            ->map(fn (OrderItem $item) => [
                $item->order->created_at?->format('Y-m-d H:i:s'),
                $item->order->order_number,
                $item->order->status_label,
                $item->product_name,
                $item->variant_name ?? '-',
                (int) $item->qty,
                (float) $item->price,
                (float) $item->subtotal,
                $item->order->user->name ?? '-',
            ])
            ->all();
    }

    private function productRows(): array
    {
        return Product::with(['category', 'brand'])
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                $product->sku ?? '-',
                $product->name,
                $product->category->name ?? '-',
                $product->brand->name ?? '-',
                (float) $product->price,
                (float) ($product->modal_price ?? 0),
                (int) $product->stock,
                (int) $product->sold_count,
                $product->is_active ? 'Aktif' : 'Nonaktif',
            ])
            ->all();
    }

    private function customerRows(Carbon $startDate, Carbon $endDate, ?string $status): array
    {
        return User::query()
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.created_at',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(orders.total), 0) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_at')
            )
            ->leftJoin('orders', function ($join) use ($startDate, $endDate, $status) {
                $join->on('orders.user_id', '=', 'users.id')
                    ->whereNull('orders.deleted_at')
                    ->whereBetween('orders.created_at', [$startDate, $endDate]);

                if ($status) {
                    $join->where('orders.status', $status);
                }
            })
            ->where('users.role', 'customer')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'users.created_at')
            ->orderByDesc('total_spent')
            ->get()
            ->map(fn (User $customer) => [
                $customer->name,
                $customer->email,
                $customer->phone ?? '-',
                (int) $customer->total_orders,
                (float) $customer->total_spent,
                $customer->last_order_at ? Carbon::parse($customer->last_order_at)->format('Y-m-d H:i:s') : '-',
                $customer->created_at?->format('Y-m-d H:i:s'),
            ])
            ->all();
    }

    private function filteredOrders(Carbon $startDate, Carbon $endDate, ?string $status)
    {
        return $this->applyOrderFilters(Order::query(), $startDate, $endDate, $status);
    }

    private function applyOrderFilters(Builder $query, Carbon $startDate, Carbon $endDate, ?string $status): Builder
    {
        return $query
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($status, fn (Builder $query) => $query->where('status', $status));
    }

    private function resolveDateRange(Request $request): array
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:pending,processing,shipped,delivered,cancelled,refunded'],
            'format' => ['nullable', 'in:pdf,excel'],
        ]);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        return [$startDate, $endDate];
    }

    private function resolveExportDateRange(Request $request): array
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:pending,processing,shipped,delivered,cancelled,refunded'],
            'format' => ['nullable', 'in:pdf,excel'],
        ]);

        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            return [
                Carbon::create(1970, 1, 1)->startOfDay(),
                now()->endOfDay(),
            ];
        }

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::create(1970, 1, 1)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        return [$startDate, $endDate];
    }

    private function statusLabel(?string $status): string
    {
        return [
            null => 'Semua Status',
            '' => 'Semua Status',
            'pending' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
        ][$status] ?? ucfirst((string) $status);
    }
}
