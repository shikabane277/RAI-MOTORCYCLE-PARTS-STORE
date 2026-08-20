<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method VARCHAR(50) DEFAULT 'cod'");
        }
    }
};
