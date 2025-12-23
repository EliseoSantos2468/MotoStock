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
        Schema::create('municipio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_municipio', length:355);
            $table->timestamps();
            $table->unsignedBigInteger('departamento_id');

            $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
