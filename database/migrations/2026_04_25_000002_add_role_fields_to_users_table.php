<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega relación de rol y trazabilidad a la tabla users.
     * Rol permitido: administración de migraciones.
     * Valida: mantiene compatibilidad con los usuarios existentes.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('rol_id')->nullable()->after('email')->constrained('roles')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->after('rol_id')->constrained('users')->nullOnDelete();
            $table->boolean('activo')->default(true)->after('creado_por');
            $table->string('password_hash')->nullable()->after('password');
        });

        $rolAdminMotosId = DB::table('roles')->where('nombre', 'admin_motos')->value('id');

        if ($rolAdminMotosId) {
            DB::table('users')->whereNull('rol_id')->update(['rol_id' => $rolAdminMotosId]);
        }

        DB::table('users')->whereNull('password_hash')->update(['password_hash' => DB::raw('password')]);
    }

    /**
     * Revierte los campos de roles y trazabilidad en users.
     * Rol permitido: administración de migraciones.
     * Valida: elimina únicamente las columnas añadidas por este sistema.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('creado_por');
            $table->dropConstrainedForeignId('rol_id');
            $table->dropColumn(['activo', 'password_hash']);
        });
    }
};