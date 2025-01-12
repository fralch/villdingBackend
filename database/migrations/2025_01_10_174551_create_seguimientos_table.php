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
        /* 
         LOS PROJECTOS: tiene seguimientos
         LOS SEGUIMIENTOS: tienen semanas
         LAS SEMANAS: tienen dias
         LAS DIAS: tienen actividades
        */

        /*
            TODO: calcular bien los dias en la semana y que comiencen los dias lunes de cada semana 
         */
        Schema::create('seguimientos', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('dia_id')->constrained('dias')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
