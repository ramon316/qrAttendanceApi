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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('matricula')->primary();
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('nombres')->nullable();
            $table->string('nombre_completo');
            $table->string('puesto');
            $table->string('puesto_desc');
            $table->string('departamento');
            $table->string('departamento_desc');
            $table->string('clave_adscripcion');
            $table->string('tipo_contratacion');
            $table->string('tipo_cempleado');
            $table->string('curp');
            $table->string('nss');
            $table->string('rfc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
