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
        Schema::create('trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->date('date_start')->nullable();
            $table->integer('duration_days')->nullable(); // Duración en días
            $table->boolean('status')->default(true); // Estado del seguimiento
            $table->timestamps();
    
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackings');
    }
};
