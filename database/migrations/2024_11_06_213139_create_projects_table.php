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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); // Nombre del proyecto
            $table->string('location'); // Ubicación del proyecto
            $table->string('company'); // Nombre de la compañía
            $table->string('code'); // Código del proyecto
            $table->date('start_date'); // Fecha de inicio
            $table->date('end_date'); // Fecha de finalización
            $table->string('uri')->nullable(); // Campo opcional de URI
            $table->timestamps(); // Campos created_at y updated_at

            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->foreignId('project_subtype_id')->constrained('project_subtypes')->onDelete('cascade')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
