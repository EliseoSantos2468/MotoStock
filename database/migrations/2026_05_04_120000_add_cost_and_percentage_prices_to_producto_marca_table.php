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
        Schema::table('producto_marca', function (Blueprint $table) {
            $table->decimal('precio_costo', 12, 2)->nullable()->after('cantidad_mayoreo');
            $table->decimal('porcentaje_publico', 8, 2)->nullable()->after('precio_costo');
            $table->decimal('porcentaje_mayoreo', 8, 2)->nullable()->after('porcentaje_publico');
            $table->decimal('porcentaje_taller', 8, 2)->nullable()->after('porcentaje_mayoreo');
            $table->decimal('precio_taller', 12, 2)->nullable()->after('precio_mayoreo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_marca', function (Blueprint $table) {
            $table->dropColumn([
                'precio_costo',
                'porcentaje_publico',
                'porcentaje_mayoreo',
                'porcentaje_taller',
                'precio_taller',
            ]);
        });
    }
};
