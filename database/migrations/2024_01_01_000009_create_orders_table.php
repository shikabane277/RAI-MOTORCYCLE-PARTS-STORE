<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. MB-20240001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            // Guest info (for guest checkout)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 20)->nullable();
            // Shipping address snapshot
            $table->string('ship_recipient')->nullable();
            $table->string('ship_phone', 20)->nullable();
            $table->string('ship_line1')->nullable();
            $table->string('ship_barangay')->nullable();
            $table->string('ship_city')->nullable();
            $table->string('ship_province')->nullable();
            $table->string('ship_region')->nullable();
            $table->string('ship_zip', 10)->nullable();
            // Financials
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            // Payment & Fulfillment
            $table->string('payment_method', 50)->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('status', ['pending_payment', 'confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'return_requested', 'refunded'])->default('pending_payment');
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable(); // customer notes
            $table->text('admin_notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
