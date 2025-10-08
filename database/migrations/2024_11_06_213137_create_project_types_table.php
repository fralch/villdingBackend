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
        Schema::create('project_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique(); // Nombre del tipo de proyecto
            $table->timestamps();
        });
         
        // Insertar datos en la tabla project_types
        $now = Carbon::now();

        DB::table('project_types')->insert([
            ['name' => 'Edificación Urbana', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Infraestructura de Transporte', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Infraestructura Portuaria', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Industrial', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Comercial', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Educativo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Salud', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Recreativo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Otros', 'created_at' => $now, 'updated_at' => $now],
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