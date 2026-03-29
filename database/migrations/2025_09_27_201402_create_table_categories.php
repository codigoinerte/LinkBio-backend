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
        // La convención es nombrar las tablas en plural: 'categories'
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200); // Es más descriptivo llamar a esta columna 'name'
            $table->string('icon', 100)->nullable();
            $table->string('image', 250)->nullable();
            $table->string('description', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
