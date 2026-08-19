<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycle_models', function (Blueprint $table) {
            $table->id();
            $table->string('make');   // Yamaha, Honda, Kawasaki, etc.
            $table->string('model');  // Sniper 155, Click 160, etc.
            $table->unsignedSmallInteger('year_start')->nullable();
            $table->unsignedSmallInteger('year_end')->nullable();
            $table->unsignedSmallInteger('engine_cc')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycle_models');
    }
};
