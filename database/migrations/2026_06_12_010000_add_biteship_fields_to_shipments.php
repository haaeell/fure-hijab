<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('biteship_order_id')->nullable()->after('order_id');
            $table->string('label_url')->nullable()->after('resi');
            $table->json('biteship_payload')->nullable()->after('tracking_history');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['biteship_order_id', 'label_url', 'biteship_payload']);
        });
    }
};
