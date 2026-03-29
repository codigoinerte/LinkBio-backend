<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Es una convención de Laravel nombrar las tablas en plural: 'links'
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true); // Usar boolean es más semántico
            $table->integer('order')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('visits')->default(0);
            $table->foreignId('category_id')->constrained(); // Asume que tienes una tabla 'categories'
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
