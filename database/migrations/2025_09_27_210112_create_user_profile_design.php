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
        // Convención: snake_case y plural. 'user_designs' es más conciso.
        Schema::create('user_designs', function (Blueprint $table) {
            $table->id();

            // Define la clave foránea correctamente, con eliminación en cascada.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Asume una tabla 'themes'. Es nullable por si el usuario no elige un tema predefinido.
            $table->string('theme_id')->nullable();
            $table->foreign('theme_id')->references('id')->on('themes')->nullOnDelete();

            $table->string('wallpaper_type')->nullable(); // tipos de wallpapers: color, patron, imagen personalizada, etc. Puede ser un enum o una referencia a otra tabla si se necesitan más detalles.

            $table->string('wallpaper_file')->nullable(); // Para almacenar la ruta del archivo de imagen personalizada, si se usa ese tipo de wallpaper.

            $table->string('wallpaper_color_type')->nullable(); // Para almacenar un ID de color predefinido, si se usa ese tipo de wallpaper. Podría ser una referencia a una tabla de colores predefinidos.
            $table->string('wallpaper_color_custom')->nullable(); // Para almacenar un color personalizado en formato hexadecimal, si se usa ese tipo de wallpaper.

            $table->string('wallpaper_pattern_type')->nullable(); // patrones predefinidos, como 'dots', 'stripes', etc. Podría ser un enum o una referencia a otra tabla si se necesitan más detalles.

            // TODO: Next future: Añadir campos para personalización avanzada. Por ejemplo:
            // $table->string('wallpaper_type')->default('color'); // 'color', 'gradient', 'image'. Más legible que usar enteros.
            // $table->string('wallpaper_value')->nullable(); // URL for image, hex code for color, CSS gradient for gradient
            // $table->string('font_family')->default('Arial, sans-serif');
            // $table->string('font_color')->default('#000000'); // Hex code
            // $table->string('button_style')->default('rounded'); // e.g., rounded, square
            // $table->string('button_color')->default('#007BFF'); // Hex code
            // $table->string('button_font_color')->default('#FFFFFF'); // Buena idea añadir el color de la fuente del botón.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_designs');
    }
};
