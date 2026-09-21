<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de libros del sistema.
     * Rol permitido: administración de migraciones.
     * Valida: soporta stock total y stock disponible para préstamos.
     */
    public function up(): void
    {
        Schema::create('libro', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('autor');
            $table->string('isbn')->unique();
            $table->string('categoria');
            $table->string('editorial');
            $table->integer('anio_publicacion');
            $table->integer('stock_total')->default(0);
            $table->integer('stock_disponible')->default(0);
            $table->decimal('precio', 12, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla de libros.
     * Rol permitido: administración de migraciones.
     * Valida: revierte el catálogo de librería.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro');
    }
};