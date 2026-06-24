<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pashmina',   'slug' => 'pashmina'],
            ['name' => 'Segi Empat', 'slug' => 'segi-empat'],
            ['name' => 'Bergo',      'slug' => 'bergo'],
            ['name' => 'Aksesoris',  'slug' => 'aksesoris'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $brands = [
            ['name' => 'FURE Exclusive', 'slug' => 'fure-exclusive'],
            ['name' => 'Daily Hijab', 'slug' => 'daily-hijab'],
        ];

        foreach ($brands as $br) {
            Brand::updateOrCreate(['slug' => $br['slug']], $br);
        }

        $catId = Category::first()->id;
        $brandId = Brand::first()->id;

        $products = [
            [
                'name' => 'Pashmina Silk Premium',
                'description' => 'Bahan silk premium lembut dan berkilau mewah.',
                'price' => 155000,
                'stock' => 100,
                'sold_count' => 45,
                'has_variant' => true,
                'variants' => [
                    ['name' => 'Dusty Rose - L', 'price' => 155000, 'stock' => 50, 'attributes' => [['name' => 'Warna', 'value' => 'Dusty Rose'], ['name' => 'Ukuran', 'value' => 'L']]],
                    ['name' => 'Midnight Blue - L', 'price' => 160000, 'stock' => 50, 'attributes' => [['name' => 'Warna', 'value' => 'Midnight Blue'], ['name' => 'Ukuran', 'value' => 'L']]],
                ]
            ],
            [
                'name' => 'Voal Square Ultra Fine',
                'description' => 'Tegak di dahi dan tidak mudah kusut.',
                'price' => 89000,
                'stock' => 200,
                'sold_count' => 120,
                'has_variant' => false,
            ],
            [
                'name' => 'Bergo Maryam Instan',
                'description' => 'Praktis untuk harian dengan bahan diamond stretch.',
                'price' => 45000,
                'stock' => 500,
                'sold_count' => 300,
                'has_variant' => true,
                'variants' => [
                    ['name' => 'Hitam', 'price' => 45000, 'stock' => 250, 'attributes' => [['name' => 'Warna', 'value' => 'Hitam']]],
                    ['name' => 'Maroon', 'price' => 45000, 'stock' => 250, 'attributes' => [['name' => 'Warna', 'value' => 'Maroon']]],
                ]
            ],
            [
                'name' => 'Ciput Rajut Anti Pusing',
                'description' => 'Inner hijab bahan rajut berkualitas.',
                'price' => 15000,
                'stock' => 1000,
                'sold_count' => 850,
                'has_variant' => false,
            ],
        ];

        foreach ($products as $pData) {
            $variants = $pData['variants'] ?? [];
            unset($pData['variants']);

            $product = Product::create(array_merge($pData, [
                'category_id' => $catId,
                'brand_id' => $brandId,
                'slug' => Str::slug($pData['name']),
                'sku' => 'SKU-' . strtoupper(Str::random(6)),
                'is_active' => true,
            ]));

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=600',
                'is_primary' => true
            ]);

            if ($product->has_variant) {
                foreach ($variants as $v) {
                    $attrs = $v['attributes'] ?? [];
                    unset($v['attributes']);
                    $variant = ProductVariant::create(array_merge($v, [
                        'product_id' => $product->id,
                        'sku' => 'VAR-' . strtoupper(Str::random(6)),
                    ]));
                    foreach ($attrs as $attr) {
                        VariantAttribute::create([
                            'variant_id'      => $variant->id,
                            'attribute_name'  => $attr['name'],
                            'attribute_value' => $attr['value'],
                        ]);
                    }
                }
            }
        }
    }
}
