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
        Schema::create('event_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->enum('zone', [
                'chihuahua',
                'hidalgo_del_parral',
                'delicias',
                'cuauhtemoc',
                'juarez',
                'nuevo_casas_grandes'
            ]);
            $table->unsignedInteger('adults')->default(0);
            $table->unsignedInteger('teenagers')->default(0);
            $table->unsignedInteger('children')->default(0)->comment('11 years old or less');
            $table->timestamps();

            // Ensure one confirmation per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_confirmations');
    }
};
