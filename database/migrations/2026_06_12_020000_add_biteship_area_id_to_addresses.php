<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user_addresses', 'biteship_area_id')) {
            Schema::table('user_addresses', function (Blueprint $table) {
                $table->string('biteship_area_id')->nullable()->after('postal_code');
            });
        }

        if (!Schema::hasColumn('order_addresses', 'biteship_area_id')) {
            Schema::table('order_addresses', function (Blueprint $table) {
                $table->string('biteship_area_id')->nullable()->after('postal_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_addresses', 'biteship_area_id')) {
            Schema::table('order_addresses', function (Blueprint $table) {
                $table->dropColumn('biteship_area_id');
            });
        }

        if (Schema::hasColumn('user_addresses', 'biteship_area_id')) {
            Schema::table('user_addresses', function (Blueprint $table) {
                $table->dropColumn('biteship_area_id');
            });
        }
    }
};
