<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de préstamos de libros.
     * Rol permitido: administración de migraciones.
     * Valida: registra quién solicitó el libro y qué admin operó el préstamo.
     */
    public function up(): void
    {
        Schema::create('prestamo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libro')->cascadeOnDelete();
            $table->text('usuario_solicitante');
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_esperada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->string('estado')->default('activo');
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de préstamos.
     * Rol permitido: administración de migraciones.
     * Valida: revierte el control de préstamos y devoluciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamo');
    }
};