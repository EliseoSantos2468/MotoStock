<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de motos del sistema.
     * Rol permitido: administración de migraciones.
     * Valida: agrega campos para inventario, identificación y estado.
     */
    public function up(): void
    {
        Schema::create('moto', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->string('color');
            $table->decimal('precio', 12, 2);
            $table->integer('stock')->default(0);
            $table->string('num_chasis')->unique();
            $table->string('num_motor')->unique();
            $table->string('estado')->default('disponible');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla de motos.
     * Rol permitido: administración de migraciones.
     * Valida: revierte el catálogo de motos y su inventario.
     */
    public function down(): void
    {
        Schema::dropIfExists('moto');
    }
};