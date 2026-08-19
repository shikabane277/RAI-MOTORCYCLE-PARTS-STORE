<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->json('photos')->nullable(); // array of image URLs
            $table->string('bike_model')->nullable(); // "Yamaha Sniper 155" — self-reported
            $table->enum('status', ['pending', 'approved', 'hidden'])->default('pending');
            $table->text('admin_reply')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
