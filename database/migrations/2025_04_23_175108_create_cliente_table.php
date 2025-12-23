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
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombres_cliente', length:255);
            $table->string('apellidos_cliente', length:255);
            $table->string('dui_cliente', length:455);
            $table->string('telefono_cliente', length:455);
            $table->string('nit_cliente', length:455);
            $table->string('email_cliente', length:455);
            $table->decimal('monto_max', 12, 2);
            $table->string('barrio', length:355);
            $table->unsignedBigInteger('id_clasificacion');
            $table->unsignedBigInteger('id_departamento');
            $table->unsignedBigInteger('id_municipio');
            $table->timestamps();

            $table->foreign('id_clasificacion')->references('id')->on('clasificacion')->onDelete('cascade');
            $table->foreign('id_departamento')->references('id')->on('departamento')->onDelete('cascade');
            $table->foreign('id_municipio')->references('id')->on('municipio')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
