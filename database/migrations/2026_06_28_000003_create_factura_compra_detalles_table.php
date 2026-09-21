<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_compra_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('marca_id');
            $table->integer('cantidad_esperada');
            $table->integer('cantidad_recibida')->default(0);
            $table->decimal('precio_unitario', 12, 2);
            $table->timestamps();

            $table->foreign('factura_compra_id')->references('id')->on('facturas_compra')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('marca_id')->references('id')->on('marca')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_compra_detalles');
    }
};
