<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'categories_active_sort_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'sold_count', 'created_at'], 'products_active_sold_created_index');
            $table->index(['is_active', 'created_at'], 'products_active_created_index');
            $table->index(['is_active', 'price'], 'products_active_price_index');
            $table->index(['category_id', 'is_active', 'created_at'], 'products_category_active_created_index');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id', 'is_primary', 'sort_order'], 'product_images_primary_sort_index');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'price'], 'product_variants_product_price_index');
        });

        Schema::table('collection_product', function (Blueprint $table) {
            $table->index('product_id', 'collection_product_product_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_created_index');
        });

        Schema::table('collection_product', function (Blueprint $table) {
            $table->dropIndex('collection_product_product_index');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_product_price_index');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('product_images_primary_sort_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_sold_created_index');
            $table->dropIndex('products_active_created_index');
            $table->dropIndex('products_active_price_index');
            $table->dropIndex('products_category_active_created_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_sort_index');
        });
    }
};
