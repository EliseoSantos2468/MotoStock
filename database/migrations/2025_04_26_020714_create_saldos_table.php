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
        Schema::create('saldos', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_mora', 10, 2)->nullable(); // Saldo total
            $table->decimal('saldo_p_interes', 10, 2); // Saldo de la cuota
            $table->decimal('saldo_pendiente', 10, 2); 
            $table->unsignedBigInteger('credito_id'); 
            $table->timestamps();

            $table->foreign('credito_id')->references('id')->on('credito')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos');
    }
};
