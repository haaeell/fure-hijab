<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE payments
            MODIFY status ENUM(
                'pending',
                'under_review',
                'success',
                'failed',
                'expired',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE payments
            MODIFY status ENUM(
                'pending',
                'success',
                'failed',
                'expired',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
