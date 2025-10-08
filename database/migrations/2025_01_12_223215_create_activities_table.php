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
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');  
            $table->unsignedBigInteger('tracking_id')->nullable();
            $table->string('name'); // Nombre de la actividad
            $table->text('description')->nullable(); // Descripción de la actividad
            $table->string('location')->nullable(); // Ubicación de la actividad
            $table->string('horas')->nullble(); // Horas de inicio y fin de la actividad
            $table->string('status')->default('pendiente'); // pendiente, en progreso, finalizado
            $table->string('icon')->nullable(); // Icono relacionado con la actividad
            $table->string('image')->nullable(); // Imagen asociada a la actividad
            $table->text('comments')->nullable(); // Comentarios opcionales
            $table->date('fecha_creacion')->nullable(); // Fecha de creación de la actividad
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade'); // Relación con el modelo Project
            $table->foreign('tracking_id')->references('id')->on('trackings')->onDelete('cascade')->nullable(); // Relación con el modelo Tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
