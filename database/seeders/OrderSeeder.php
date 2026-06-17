<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Review;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@mail.com')->first();
        $product1 = Product::where('slug', 'voal-square-ultra-fine')->first();
        $product2 = Product::where('slug', 'pashmina-silk-premium')->first();
        $variant1 = ProductVariant::where('name', 'Dusty Rose - L')->first();
        $variant2 = ProductVariant::where('name', 'Midnight Blue - L')->first();
        $coupon   = Coupon::where('code', 'DISKON10')->first();

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 1 — Delivered, lunas, sudah ada review
        // ══════════════════════════════════════════════════════════════════════
        $order1 = Order::create([
            'user_id'       => $customer->id,
            'order_number'  => 'ORD-20240310-A1B2C',
            'status'        => 'delivered',
            'subtotal'      => 85000,
            'shipping_cost' => 12000,
            'discount'      => 8500,
            'total'         => 88500,
            'notes'         => 'Tolong dibungkus rapi ya kak.',
            'coupon_id'     => $coupon->id,
            'coupon_code'   => 'DISKON10',
            'created_at'    => Carbon::now()->subDays(20),
            'updated_at'    => Carbon::now()->subDays(14),
        ]);

        // Items
        $item1a = OrderItem::create([
            'order_id'     => $order1->id,
            'product_id'   => $product1->id,
            'variant_id'   => null,
            'product_name' => 'Hijab Basic Cream',
            'variant_name' => null,
            'qty'          => 1,
            'price'        => 35000,
            'subtotal'     => 35000,
        ]);

        $item1b = OrderItem::create([
            'order_id'     => $order1->id,
            'product_id'   => $product2->id,
            'variant_id'   => $variant1->id,
            'product_name' => 'Hijab Voal Premium',
            'variant_name' => 'Cream - L',
            'qty'          => 1,
            'price'        => 50000,
            'subtotal'     => 50000,
        ]);

        // Address
        OrderAddress::create([
            'order_id'      => $order1->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        // Payment — success
        Payment::create([
            'order_id'               => $order1->id,
            'midtrans_order_id'      => 'ORD-20240310-A1B2C',
            'midtrans_transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'snap_token'             => null,
            'payment_method'         => 'gopay',
            'status'                 => 'success',
            'amount'                 => 88500,
            'paid_at'                => Carbon::now()->subDays(19),
            'payload'                => json_encode([
                'transaction_status' => 'settlement',
                'payment_type'       => 'gopay',
                'gross_amount'       => '88500.00',
            ]),
            'expired_at'             => Carbon::now()->subDays(19)->addDay(),
        ]);

        // Shipment — delivered
        Shipment::create([
            'order_id'               => $order1->id,
            'courier'                => 'jne',
            'service'                => 'REG',
            'service_code'           => 'JNE-REG',
            'cost'                   => 12000,
            'total_weight'           => 500,
            'resi'                   => 'JNE1234567890ID',
            'status'                 => 'delivered',
            'origin_city_id'         => '501',
            'destination_city_id'    => '444',
            'estimated_days'         => '2-3',
            'tracking_history'       => json_encode([
                [
                    'date'        => Carbon::now()->subDays(18)->format('d-m-Y'),
                    'time'        => '08:30',
                    'description' => 'Paket telah diterima di gudang JNE Jakarta Selatan',
                    'location'    => 'Jakarta Selatan',
                ],
                [
                    'date'        => Carbon::now()->subDays(17)->format('d-m-Y'),
                    'time'        => '14:20',
                    'description' => 'Paket dalam perjalanan ke kota tujuan',
                    'location'    => 'Transit - Semarang',
                ],
                [
                    'date'        => Carbon::now()->subDays(16)->format('d-m-Y'),
                    'time'        => '09:45',
                    'description' => 'Paket tiba di gudang JNE Yogyakarta',
                    'location'    => 'Yogyakarta',
                ],
                [
                    'date'        => Carbon::now()->subDays(15)->format('d-m-Y'),
                    'time'        => '11:10',
                    'description' => 'Paket sedang dalam proses pengiriman ke alamat tujuan',
                    'location'    => 'Yogyakarta',
                ],
                [
                    'date'        => Carbon::now()->subDays(14)->format('d-m-Y'),
                    'time'        => '15:30',
                    'description' => 'Paket telah diterima oleh Customer Demo',
                    'location'    => 'Sosromenduran, Yogyakarta',
                ],
            ]),
        ]);

        // Reviews
        Review::create([
            'user_id'       => $customer->id,
            'product_id'    => $product1->id,
            'order_item_id' => $item1a->id,
            'rating'        => 5,
            'comment'       => 'Bahannya lembut banget, warnanya juga sesuai foto. Pengiriman cepat!',
            'images'        => null,
            'is_verified'   => true,
            'created_at'    => Carbon::now()->subDays(12),
        ]);

        Review::create([
            'user_id'       => $customer->id,
            'product_id'    => $product2->id,
            'order_item_id' => $item1b->id,
            'rating'        => 4,
            'comment'       => 'Kualitas oke, tapi warna sedikit berbeda dari foto. Overall puas.',
            'images'        => null,
            'is_verified'   => true,
            'created_at'    => Carbon::now()->subDays(11),
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 2 — Shipped, sedang dikirim, belum review
        // ══════════════════════════════════════════════════════════════════════
        $order2 = Order::create([
            'user_id'       => $customer->id,
            'order_number'  => 'ORD-20240325-D3E4F',
            'status'        => 'shipped',
            'subtotal'      => 110000,
            'shipping_cost' => 15000,
            'discount'      => 0,
            'total'         => 125000,
            'notes'         => null,
            'coupon_id'     => null,
            'created_at'    => Carbon::now()->subDays(5),
            'updated_at'    => Carbon::now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id'     => $order2->id,
            'product_id'   => $product2->id,
            'variant_id'   => $variant2->id,
            'product_name' => 'Hijab Voal Premium',
            'variant_name' => 'Hitam - XL',
            'qty'          => 2,
            'price'        => 55000,
            'subtotal'     => 110000,
        ]);

        OrderAddress::create([
            'order_id'      => $order2->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        Payment::create([
            'order_id'               => $order2->id,
            'midtrans_order_id'      => 'ORD-20240325-D3E4F',
            'midtrans_transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'snap_token'             => null,
            'payment_method'         => 'bca_va',
            'status'                 => 'success',
            'amount'                 => 125000,
            'paid_at'                => Carbon::now()->subDays(4),
            'payload'                => json_encode([
                'transaction_status' => 'settlement',
                'payment_type'       => 'bank_transfer',
                'va_numbers'         => [['bank' => 'bca', 'va_number' => '12345678901234']],
                'gross_amount'       => '125000.00',
            ]),
            'expired_at'             => Carbon::now()->subDays(4)->addDay(),
        ]);

        Shipment::create([
            'order_id'            => $order2->id,
            'courier'             => 'sicepat',
            'service'             => 'BEST',
            'service_code'        => 'SICEPAT-BEST',
            'cost'                => 15000,
            'total_weight'        => 1000,
            'resi'                => 'SCPT009876543',
            'status'              => 'in_transit',
            'origin_city_id'      => '501',
            'destination_city_id' => '444',
            'estimated_days'      => '1-2',
            'tracking_history'    => json_encode([
                [
                    'date'        => Carbon::now()->subDays(3)->format('d-m-Y'),
                    'time'        => '10:00',
                    'description' => 'Paket diterima di hub SiCepat Jakarta',
                    'location'    => 'Jakarta',
                ],
                [
                    'date'        => Carbon::now()->subDays(2)->format('d-m-Y'),
                    'time'        => '18:00',
                    'description' => 'Paket dalam perjalanan',
                    'location'    => 'Transit - Solo',
                ],
                [
                    'date'        => Carbon::now()->subDays(1)->format('d-m-Y'),
                    'time'        => '07:30',
                    'description' => 'Paket tiba di hub SiCepat Yogyakarta, siap antar',
                    'location'    => 'Yogyakarta',
                ],
            ]),
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 3 — Processing, sudah dibayar, belum ada resi
        // ══════════════════════════════════════════════════════════════════════
        $order3 = Order::create([
            'user_id'       => $customer->id,
            'order_number'  => 'ORD-20240401-G5H6I',
            'status'        => 'processing',
            'subtotal'      => 35000,
            'shipping_cost' => 10000,
            'discount'      => 0,
            'total'         => 45000,
            'notes'         => null,
            'coupon_id'     => null,
            'created_at'    => Carbon::now()->subDays(2),
            'updated_at'    => Carbon::now()->subDays(1),
        ]);

        OrderItem::create([
            'order_id'     => $order3->id,
            'product_id'   => $product1->id,
            'variant_id'   => null,
            'product_name' => 'Hijab Basic Cream',
            'variant_name' => null,
            'qty'          => 1,
            'price'        => 35000,
            'subtotal'     => 35000,
        ]);

        OrderAddress::create([
            'order_id'      => $order3->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        Payment::create([
            'order_id'               => $order3->id,
            'midtrans_order_id'      => 'ORD-20240401-G5H6I',
            'midtrans_transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'snap_token'             => null,
            'payment_method'         => 'qris',
            'status'                 => 'success',
            'amount'                 => 45000,
            'paid_at'                => Carbon::now()->subDays(1),
            'payload'                => json_encode([
                'transaction_status' => 'settlement',
                'payment_type'       => 'qris',
                'gross_amount'       => '45000.00',
            ]),
            'expired_at'             => Carbon::now()->subDays(1)->addDay(),
        ]);

        // Shipment record dibuat tapi belum ada resi
        Shipment::create([
            'order_id'            => $order3->id,
            'courier'             => 'jnt',
            'service'             => 'EZ',
            'service_code'        => 'JNT-EZ',
            'cost'                => 10000,
            'total_weight'        => 300,
            'resi'                => null,
            'status'              => 'pending',
            'origin_city_id'      => '501',
            'destination_city_id' => '444',
            'estimated_days'      => '2-3',
            'tracking_history'    => json_encode([]),
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 4 — Pending, sudah buat payment tapi belum bayar
        // ══════════════════════════════════════════════════════════════════════
        $order4 = Order::create([
            'user_id'       => $customer->id,
            'order_number'  => 'ORD-20240402-J7K8L',
            'status'        => 'pending',
            'subtotal'      => 55000,
            'shipping_cost' => 12000,
            'discount'      => 5000,
            'total'         => 62000,
            'notes'         => null,
            'coupon_id'     => Coupon::where('code', 'HEMAT5000')->first()?->id,
            'coupon_code'   => 'HEMAT5000',
            'created_at'    => Carbon::now()->subHours(3),
            'updated_at'    => Carbon::now()->subHours(3),
        ]);

        OrderItem::create([
            'order_id'     => $order4->id,
            'product_id'   => $product2->id,
            'variant_id'   => $variant1->id,
            'product_name' => 'Hijab Voal Premium',
            'variant_name' => 'Cream - L',
            'qty'          => 1,
            'price'        => 50000,
            'subtotal'     => 50000,
        ]);

        // Tambah item ke-2 biar lebih realistis
        OrderItem::create([
            'order_id'     => $order4->id,
            'product_id'   => $product1->id,
            'variant_id'   => null,
            'product_name' => 'Hijab Basic Cream',
            'variant_name' => null,
            'qty'          => 1,
            'price'        => 5000,   // sample harga tambahan
            'subtotal'     => 5000,
        ]);

        OrderAddress::create([
            'order_id'      => $order4->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        // Snap token masih aktif, belum bayar
        Payment::create([
            'order_id'               => $order4->id,
            'midtrans_order_id'      => 'ORD-20240402-J7K8L',
            'midtrans_transaction_id' => null,
            'snap_token'             => 'snap-token-dummy-' . Str::random(20),
            'payment_method'         => null,
            'status'                 => 'pending',
            'amount'                 => 62000,
            'paid_at'                => null,
            'payload'                => null,
            'expired_at'             => Carbon::now()->addHours(21), // 24 jam dari buat
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 5 — Cancelled
        // ══════════════════════════════════════════════════════════════════════
        $order5 = Order::create([
            'user_id'              => $customer->id,
            'order_number'         => 'ORD-20240320-M9N0O',
            'status'               => 'cancelled',
            'subtotal'             => 50000,
            'shipping_cost'        => 12000,
            'discount'             => 0,
            'total'                => 62000,
            'notes'                => 'Salah pilih ukuran.',
            'coupon_id'            => null,
            'cancellation_reason'  => 'Salah pilih ukuran, ingin order ulang.',
            'cancelled_at'         => Carbon::now()->subDays(11),
            'cancelled_by'         => 'customer',
            'created_at'           => Carbon::now()->subDays(12),
            'updated_at'           => Carbon::now()->subDays(11),
        ]);

        OrderItem::create([
            'order_id'     => $order5->id,
            'product_id'   => $product2->id,
            'variant_id'   => $variant2->id,
            'product_name' => 'Hijab Voal Premium',
            'variant_name' => 'Hitam - XL',
            'qty'          => 1,
            'price'        => 55000,
            'subtotal'     => 55000,
        ]);

        OrderAddress::create([
            'order_id'      => $order5->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        // Payment expired/failed
        Payment::create([
            'order_id'               => $order5->id,
            'midtrans_order_id'      => 'ORD-20240320-M9N0O',
            'midtrans_transaction_id' => null,
            'snap_token'             => null,
            'payment_method'         => null,
            'status'                 => 'expired',
            'amount'                 => 62000,
            'paid_at'                => null,
            'payload'                => json_encode(['transaction_status' => 'expire']),
            'expired_at'             => Carbon::now()->subDays(11),
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // ORDER 6 — Confirmed, baru dikonfirmasi admin belum diproses
        // ══════════════════════════════════════════════════════════════════════
        $order6 = Order::create([
            'user_id'       => $customer->id,
            'order_number'  => 'ORD-20240403-P1Q2R',
            'status'        => 'confirmed',
            'subtotal'      => 100000,
            'shipping_cost' => 15000,
            'discount'      => 10000,
            'total'         => 105000,
            'notes'         => null,
            'coupon_id'     => $coupon->id,
            'coupon_code'   => 'DISKON10',
            'created_at'    => Carbon::now()->subHours(8),
            'updated_at'    => Carbon::now()->subHours(1),
        ]);

        OrderItem::create([
            'order_id'     => $order6->id,
            'product_id'   => $product2->id,
            'variant_id'   => $variant1->id,
            'product_name' => 'Hijab Voal Premium',
            'variant_name' => 'Cream - L',
            'qty'          => 2,
            'price'        => 50000,
            'subtotal'     => 100000,
        ]);

        OrderAddress::create([
            'order_id'      => $order6->id,
            'type'          => 'shipping',
            'receiver_name' => 'Customer Demo',
            'phone'         => '08123456789',
            'address'       => 'Jl. Malioboro No. 45, RT 02/RW 03',
            'province'      => 'DI Yogyakarta',
            'city'          => 'Kota Yogyakarta',
            'district'      => 'Gedongtengen',
            'subdistrict'   => 'Sosromenduran',
            'postal_code'   => '55271',
        ]);

        Payment::create([
            'order_id'               => $order6->id,
            'midtrans_order_id'      => 'ORD-20240403-P1Q2R',
            'midtrans_transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'snap_token'             => null,
            'payment_method'         => 'mandiri_va',
            'status'                 => 'success',
            'amount'                 => 105000,
            'paid_at'                => Carbon::now()->subHours(6),
            'payload'                => json_encode([
                'transaction_status' => 'settlement',
                'payment_type'       => 'echannel',
                'gross_amount'       => '105000.00',
            ]),
            'expired_at'             => Carbon::now()->subHours(6)->addDay(),
        ]);
    }
}
