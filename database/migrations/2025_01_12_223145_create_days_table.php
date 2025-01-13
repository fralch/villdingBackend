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
        Schema::create('days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tracking_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('week_id');
            $table->date('date'); // Fecha del día
            $table->timestamps();

            $table->foreign('tracking_id')->references('id')->on('trackings')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('week_id')->references('id')->on('weeks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
