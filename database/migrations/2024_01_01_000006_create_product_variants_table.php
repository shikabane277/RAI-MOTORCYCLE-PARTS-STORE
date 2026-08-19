<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('variant_sku')->unique();
            $table->string('thread_size')->nullable();   // M5, M6, M8, M10, M12
            $table->string('thread_pitch')->nullable();  // 0.8, 1.0, 1.25, 1.5, 1.75
            $table->unsignedSmallInteger('length_mm')->nullable();
            $table->string('head_type')->nullable();     // hex, flange, button, tapered, countersunk
            $table->string('material')->nullable();      // Stainless A2, Titanium Gr5, 7075 Aluminum, Chromoly
            $table->string('color')->nullable();         // raw, black, red, blue, gold, rainbow, purple
            $table->string('finish')->nullable();        // anodized, polished, raw, coated
            $table->unsignedSmallInteger('pack_qty')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->string('image_url')->nullable();
            $table->json('images')->nullable(); // additional images array
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
