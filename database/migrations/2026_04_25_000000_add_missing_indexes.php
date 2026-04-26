<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega índices críticos para optimizar queries frecuentes
     */
    public function up(): void
    {
        // Índices en tabla cliente
        Schema::table('cliente', function (Blueprint $table) {
            // Búsquedas por DUI en ListaClientes
            if (!Schema::hasIndex('cliente', 'cliente_dui_cliente_index')) {
                $table->index('dui_cliente', 'cliente_dui_cliente_index');
            }
            
            // Búsquedas por nombres
            if (!Schema::hasIndex('cliente', 'cliente_nombres_index')) {
                $table->index('nombres_cliente', 'cliente_nombres_index');
            }
            
            // Búsquedas por clasificación
            if (!Schema::hasIndex('cliente', 'cliente_clasificacion_index')) {
                $table->index('id_clasificacion', 'cliente_clasificacion_index');
            }
        });

        // Índices en tabla recibos
        Schema::table('recibos', function (Blueprint $table) {
            // Filtrado por cliente (eager loading Cliente::with(['recibos']))
            if (!Schema::hasIndex('recibos', 'recibos_cliente_index')) {
                $table->index('id_cliente', 'recibos_cliente_index');
            }
            
            // Reportes y filtros por fecha
            if (!Schema::hasIndex('recibos', 'recibos_fecha_index')) {
                $table->index('fecha', 'recibos_fecha_index');
            }
            
            // Rango de fechas en reportes
            if (!Schema::hasIndex('recibos', 'recibos_cliente_fecha_index')) {
                $table->index(['id_cliente', 'fecha'], 'recibos_cliente_fecha_index');
            }
        });

        // Índices en tabla producto_marca (pivot table)
        Schema::table('producto_marca', function (Blueprint $table) {
            // Consultas por producto (BelongsToMany)
            if (!Schema::hasIndex('producto_marca', 'pm_producto_index')) {
                $table->index('producto_id', 'pm_producto_index');
            }
            
            // Consultas por marca
            if (!Schema::hasIndex('producto_marca', 'pm_marca_index')) {
                $table->index('marca_id', 'pm_marca_index');
            }
            
            // Búsqueda específica de marca por producto (Ventas.php línea 103)
            if (!Schema::hasIndex('producto_marca', 'pm_producto_marca_unique')) {
                $table->unique(['producto_id', 'marca_id'], 'pm_producto_marca_unique');
            }
        });

        // Índices en tabla producto_recibo (pivot table)
        Schema::table('producto_recibo', function (Blueprint $table) {
            // JOIN en Recibo::with(['productos'])
            if (!Schema::hasIndex('producto_recibo', 'pr_producto_index')) {
                $table->index('producto_id', 'pr_producto_index');
            }
            
            if (!Schema::hasIndex('producto_recibo', 'pr_recibo_index')) {
                $table->index('recibo_id', 'pr_recibo_index');
            }
        });

        // Índices para dashboard y métricas (consultas de agregación por fecha)
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->dropIndexIfExists('cliente_dui_cliente_index');
            $table->dropIndexIfExists('cliente_nombres_index');
            $table->dropIndexIfExists('cliente_clasificacion_index');
        });

        Schema::table('recibos', function (Blueprint $table) {
            $table->dropIndexIfExists('recibos_cliente_index');
            $table->dropIndexIfExists('recibos_fecha_index');
            $table->dropIndexIfExists('recibos_cliente_fecha_index');
        });

        Schema::table('producto_marca', function (Blueprint $table) {
            $table->dropIndexIfExists('pm_producto_index');
            $table->dropIndexIfExists('pm_marca_index');
            $table->dropIndexIfExists('pm_producto_marca_unique');
        });

        Schema::table('producto_recibo', function (Blueprint $table) {
            $table->dropIndexIfExists('pr_producto_index');
            $table->dropIndexIfExists('pr_recibo_index');
        });

        Schema::table('credito', function (Blueprint $table) {
            $table->dropIndexIfExists('credito_cliente_index');
            $table->dropIndexIfExists('credito_interes_index');
        });
    }
};
