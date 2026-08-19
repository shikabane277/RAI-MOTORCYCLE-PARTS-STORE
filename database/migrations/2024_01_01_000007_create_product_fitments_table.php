<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_fitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('motorcycle_model_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable(); // e.g. "Fits 2019-2024 only"
            $table->timestamps();
            $table->unique(['product_id', 'motorcycle_model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_fitments');
    }
};
