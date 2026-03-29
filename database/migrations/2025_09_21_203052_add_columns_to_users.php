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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname', 50)->nullable()->after('password');
            $table->string('photo')->nullable()->after('nickname');
            $table->text('bio')->nullable()->after('photo');
            $table->string('website')->nullable()->after('bio');
            $table->string('background')->nullable()->after('website');
            $table->boolean('is_active')->default(true)->after('background');
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->softDeletes(); // Esto crea una columna 'deleted_at' de tipo timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Es mejor agrupar las eliminaciones en un array
            $table->dropColumn([
                'nickname', 'photo', 'bio', 'website', 'background',
                'is_active', 'is_verified'
            ]);
            // Y usar el método correspondiente para softDeletes
            $table->dropSoftDeletes();
        });
    }
};
