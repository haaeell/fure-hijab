<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_channel', 20)->default('midtrans')->after('order_id');
            $table->string('proof_image')->nullable()->after('snap_token');
            $table->timestamp('proof_uploaded_at')->nullable()->after('proof_image');
            $table->timestamp('reviewed_at')->nullable()->after('paid_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable()->after('payload');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'payment_channel',
                'proof_image',
                'proof_uploaded_at',
                'reviewed_at',
                'review_note',
            ]);
        });
    }
};
