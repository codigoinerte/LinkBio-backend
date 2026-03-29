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
        Schema::table('projects', function (Blueprint $table) {
            $table->string("short_description")->nullable()->after("description");
            $table->string("link")->nullable()->after("short_description");
            $table->boolean("is_enabled")->default(true)->after("to");
            $table->integer("order")->default(0)->after("is_enabled");
            $table->integer("click")->default(0)->after("order");
            $table->integer("visits")->default(0)->after("click");
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn("short_description");
            $table->dropColumn("link");
            $table->dropColumn("is_enabled");
            $table->dropColumn("order");
            $table->dropColumn("click");
            $table->dropColumn("visits");
            $table->dropForeign(["user_id"]);
            $table->dropColumn("user_id");
        });
    }
};
