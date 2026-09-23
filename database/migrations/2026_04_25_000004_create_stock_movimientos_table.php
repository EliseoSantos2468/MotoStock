<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de movimientos de stock para motos.
     * Rol permitido: administración de migraciones.
     * Valida: registra ventas, ajustes e ingresos con usuario responsable.
     */
    public function up(): void
    {
        Schema::create('stock_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moto_id')->constrained('moto')->cascadeOnDelete();
            $table->string('tipo');
            $table->integer('cantidad');
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de movimientos de stock.
     * Rol permitido: administración de migraciones.
     * Valida: limpia el historial operativo del inventario de motos.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movimientos');
    }
};