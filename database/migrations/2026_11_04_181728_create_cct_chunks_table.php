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
        Schema::create('cct_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('documento')->default('CCT IMSS-SNTSS');
            $table->string('capitulo')->index();
            $table->string('clausula')->nullable()->index();
            $table->string('tema')->nullable();
            $table->longText('contenido');
            $table->json('embedding');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cct_chunks');
    }
};
