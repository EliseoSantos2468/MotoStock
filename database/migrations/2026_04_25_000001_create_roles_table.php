<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de roles base del sistema.
     * Rol permitido: administración de migraciones.
     * Valida: deja precargados los tres perfiles requeridos.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('roles')->insert([
            [
                'nombre' => 'admin_motos',
                'descripcion' => 'Administración total del módulo de motos',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'admin_libreria',
                'descripcion' => 'Administración total del módulo de librería',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'ventas',
                'descripcion' => 'Usuario de ventas con acceso reducido al módulo de motos',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Elimina la tabla de roles.
     * Rol permitido: administración de migraciones.
     * Valida: revierte la estructura creada para el sistema de permisos.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};