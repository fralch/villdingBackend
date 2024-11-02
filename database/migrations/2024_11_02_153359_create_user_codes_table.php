<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); // Llave foránea a usuarios
            $table->string('code'); // Código de verificación
            $table->string('type')->nullable(); // Tipo de código (opcional)
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración
            $table->timestamps();

            // Definir la relación con la tabla `users`
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_codes');
    }
};
