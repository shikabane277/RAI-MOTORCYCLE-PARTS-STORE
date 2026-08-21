<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter enum column to string(50) to support 'free_shipping', 'percentage', 'fixed'
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('type', 50)->default('fixed')->change();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('type', 20)->default('fixed')->change();
        });
    }
};
