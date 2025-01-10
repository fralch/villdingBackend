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
        Schema::create('project_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique(); // Nombre del tipo de proyecto, como 'Residencial', 'Comercial', etc.
            $table->timestamps();
        });
         
        // Insertar datos en la tabla project_types
          $now = Carbon::now();

          DB::table('project_types')->insert([
              ['name' => 'Residencial', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Comercial', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Industrial', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Infraestructura', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Institucional', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Renovación y remodelación', 'created_at' => $now, 'updated_at' => $now],
              ['name' => 'Proyectos de paisajismo y diseño urbano', 'created_at' => $now, 'updated_at' => $now],
          ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_types');
    }
};
