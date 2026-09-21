<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega índices críticos para el dashboard y métricas de rendimiento
     */
    public function up(): void
    {
        // Índices para dashboard - consultas de agregación por fecha
        Schema::table('recibos', function (Blueprint $table) {
            // Índice compuesto para consultas de rango de fechas (dashboard)
            if (!Schema::hasIndex('recibos', 'recibos_fecha_total_index')) {
                $table->index(['fecha', 'total'], 'recibos_fecha_total_index');
            }
        });

        // Índices para clientes (dashboard)
        Schema::table('cliente', function (Blueprint $table) {
            // Consultas por fecha de creación (dashboard)
            if (!Schema::hasIndex('cliente', 'cliente_created_at_index')) {
                $table->index('created_at', 'cliente_created_at_index');
            }
        });

        // Índices para producto_recibo (dashboard - top productos)
        Schema::table('producto_recibo', function (Blueprint $table) {
            // JOIN + GROUP BY para top productos
            if (!Schema::hasIndex('producto_recibo', 'pr_producto_cantidad_index')) {
                $table->index(['producto_id', 'cantidad'], 'pr_producto_cantidad_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recibos', function (Blueprint $table) {
            $table->dropIndexIfExists('recibos_fecha_total_index');
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->dropIndexIfExists('cliente_created_at_index');
        });

        Schema::table('producto_recibo', function (Blueprint $table) {
            $table->dropIndexIfExists('pr_producto_cantidad_index');
        });
    }
};
