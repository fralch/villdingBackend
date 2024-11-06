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
        Schema::create('project_subtypes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade'); // Relación con 'project_types'
            $table->string('name'); // Nombre del subtipo, como 'Vivienda unifamiliar', 'Oficinas', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subtypes');
    }
};
