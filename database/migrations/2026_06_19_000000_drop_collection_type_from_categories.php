<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'collection_type')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('collection_type');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'collection_type')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->string('collection_type')->nullable()->after('sort_order')->index();
        });
    }
};
