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
        Schema::create('mothers_day_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('zone');
            $table->string('matricula')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();

            // Definimos la relación con employees si es necesario (opcional)
            $table->foreign('matricula')->references('matricula')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mothers_day_registrations');
    }
};
