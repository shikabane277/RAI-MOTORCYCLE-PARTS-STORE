<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only stock history — never update or delete rows
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('change_qty'); // positive = stock in, negative = stock out
            $table->unsignedInteger('stock_after'); // snapshot of stock after change
            $table->enum('reason', ['sale', 'return', 'restock', 'manual_adjustment', 'damaged', 'recount'])->default('manual_adjustment');
            $table->string('reference')->nullable(); // order_number or note
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
