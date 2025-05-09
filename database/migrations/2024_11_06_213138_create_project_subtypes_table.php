<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
            $table->string('name'); // Nombre del subtipo
            $table->timestamps();
        });

        // Insertar datos en la tabla project_subtypes
        $now = Carbon::now();

        DB::table('project_subtypes')->insert([
            // Edificación Urbana (ID = 1)
            ['project_type_id' => 1, 'name' => 'Vivienda Unifamiliar', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 1, 'name' => 'Edificio Multifamiliar', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 1, 'name' => 'Torre Residencial', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 1, 'name' => 'Edificio de Oficinas', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 1, 'name' => 'Hotel', 'created_at' => $now, 'updated_at' => $now],
            
            // Infraestructura de Transporte (ID = 2)
            ['project_type_id' => 2, 'name' => 'Carretera', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 2, 'name' => 'Puente', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 2, 'name' => 'Túnel', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 2, 'name' => 'Vía férrea', 'created_at' => $now, 'updated_at' => $now],
            
            // Infraestructura Portuaria (ID = 3)
            ['project_type_id' => 3, 'name' => 'Muelle', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 3, 'name' => 'Terminal portuario', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 3, 'name' => 'Dársena', 'created_at' => $now, 'updated_at' => $now],
            
            // Industrial (ID = 4)
            ['project_type_id' => 4, 'name' => 'Planta de Producción', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 4, 'name' => 'Almacén Logístico', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 4, 'name' => 'Depósito Aduanero', 'created_at' => $now, 'updated_at' => $now],
            
            // Comercial (ID = 5)
            ['project_type_id' => 5, 'name' => 'Centro Comercial', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 5, 'name' => 'Tienda de Retail', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 5, 'name' => 'Supermercado', 'created_at' => $now, 'updated_at' => $now],
            
            // Educativo (ID = 6)
            ['project_type_id' => 6, 'name' => 'Colegio', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 6, 'name' => 'Universidad', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 6, 'name' => 'Instituto Técnico', 'created_at' => $now, 'updated_at' => $now],
            
            // Salud (ID = 7)
            ['project_type_id' => 7, 'name' => 'Hospital', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 7, 'name' => 'Clínica', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 7, 'name' => 'Centro de Salud', 'created_at' => $now, 'updated_at' => $now],
            
            // Recreativo (ID = 8)
            ['project_type_id' => 8, 'name' => 'Parque', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 8, 'name' => 'Polideportivo', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 8, 'name' => 'Centro Cultural', 'created_at' => $now, 'updated_at' => $now],
            
            // Otros (ID = 9)
            ['project_type_id' => 9, 'name' => 'Proyecto Especial', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 9, 'name' => 'Equipamiento Urbano', 'created_at' => $now, 'updated_at' => $now],
            ['project_type_id' => 9, 'name' => 'Obra Temporal', 'created_at' => $now, 'updated_at' => $now],
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