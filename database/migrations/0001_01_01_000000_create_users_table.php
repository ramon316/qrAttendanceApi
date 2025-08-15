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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('employee_id')->nullable();
            $table->enum('status', ['active', 'pending_verification', 'verification_failed', 'correction_requested', 'permanently_rejected'])->default('pending_verification');
            $table->integer('verification_attempts')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        /* // Estados del usuario
        'pending_verification'     // Esperando verificación mensual
        'verification_failed'      // Matrícula no encontrada después de verificación
        'correction_requested'     // Usuario solicitó corrección
        'permanently_rejected'     // Múltiples intentos fallidos
        'active'                  // Verificado correctamente */

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
