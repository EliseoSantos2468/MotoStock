<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas_compra', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura', 100);
            $table->date('fecha');
            $table->enum('estado', ['pendiente', 'parcial', 'recibida'])->default('pendiente');
            $table->unsignedBigInteger('proveedor_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'fecha']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_compra');
    }
};
