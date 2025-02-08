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
            $table->unsignedBigInteger('day_id');
            $table->unsignedBigInteger('project_id');  
            $table->unsignedBigInteger('user_id');
            $table->string('name'); // Nombre de la actividad
            $table->text('description')->nullable(); // Descripción de la actividad
            $table->string('location')->nullable(); // Ubicación de la actividad
            $table->time('hour_start');
            $table->time('hour_end');
            $table->string('status')->default('pendiente'); // pendiente, en progreso, finalizado
            $table->string('icon')->nullable(); // Icono relacionado con la actividad
            $table->string('image')->nullable(); // Imagen asociada a la actividad
            $table->text('comments')->nullable(); // Comentarios opcionales
            $table->timestamps();

            $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
