<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('producto_marca', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('marca_id');
            $table->integer('cantidad');
            $table->decimal('precio_cliente', 12, 2);
            $table->decimal('precio_mayoreo', 12, 2);
            $table->integer('venta_producto');
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('marca_id')->references('id')->on('marca')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_marca');
    }
};
