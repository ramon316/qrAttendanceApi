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
        Schema::create('cct_chat_sessions', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('user_identifier')->nullable()->index();
            $table->string('titulo')->nullable();
            $table->timestamps();
        });

        Schema::create('cct_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->json('fuentes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('session_id')
                ->references('id')
                ->on('cct_chat_sessions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cct_chat_messages');
        Schema::dropIfExists('cct_chat_sessions');
    }
};

