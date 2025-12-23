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
        Schema::create('credito', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_facturado', 12, 2);
            $table->decimal('interes_moratorio', 12, 2);
            $table->decimal('prima', 12, 2);
            $table->unsignedBigInteger('cuota_id')->nullable();
            $table->unsignedBigInteger('interes_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('fechas_id')->nullable();
            $table->timestamps();

            $table->foreign('cuota_id')->references('id')->on('cuotas')->onDelete('cascade');
            $table->foreign('interes_id')->references('id')->on('interes')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('cliente')->onDelete('cascade');
            $table->foreign('fechas_id')->references('id')->on('fechas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credito');
    }
};
