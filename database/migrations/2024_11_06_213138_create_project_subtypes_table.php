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

          // Insertar datos en la tabla project_subtypes
          $now = Carbon::now();

          DB::table('project_subtypes')->insert([
              // Subtipos para 'Residencial' (ID = 1)
              ['project_type_id' => 1, 'name' => 'Vivienda unifamiliar', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 1, 'name' => 'Vivienda multifamiliar', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 1, 'name' => 'Urbanizaciones', 'created_at' => $now, 'updated_at' => $now],
  
              // Subtipos para 'Comercial' (ID = 2)
              ['project_type_id' => 2, 'name' => 'Oficinas', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 2, 'name' => 'Centros comerciales', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 2, 'name' => 'Restaurantes y bares', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 2, 'name' => 'Hoteles', 'created_at' => $now, 'updated_at' => $now],
  
              // Subtipos para 'Industrial' (ID = 3)
              ['project_type_id' => 3, 'name' => 'Fábricas y plantas de producción', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 3, 'name' => 'Almacenes y centros de distribución', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 3, 'name' => 'Instalaciones de energía (solar, eólica, etc.)', 'created_at' => $now, 'updated_at' => $now],
  
              // Subtipos para 'Infraestructura' (ID = 4)
              ['project_type_id' => 4, 'name' => 'Carreteras y puentes', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 4, 'name' => 'Aeropuertos', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 4, 'name' => 'Puertos y muelles', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 4, 'name' => 'Estaciones de tren y metro', 'created_at' => $now, 'updated_at' => $now],
  
              // Subtipos para 'Institucional' (ID = 5)
              ['project_type_id' => 5, 'name' => 'Escuelas y universidades', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 5, 'name' => 'Hospitales y centros de salud', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 5, 'name' => 'Edificios gubernamentales', 'created_at' => $now, 'updated_at' => $now],
              ['project_type_id' => 5, 'name' => 'Instalaciones deportivas y recreativas', 'created_at' => $now, 'updated_at' => $now],
          ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subtypes');
    }
};
