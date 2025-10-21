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
        Schema::create('pending_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('employee_matricula', 12);
            $table->decimal('user_latitude', 10, 8);
            $table->decimal('user_longitude', 11, 8);
            $table->decimal('distance_meters', 8, 2);
            $table->boolean('verified')->default(true);
            $table->timestamp('checked_in_at');
            $table->foreignId('migrated_to_attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
            $table->timestamp('migrated_at')->nullable();
            $table->timestamps();

            // Foreign key to employees table
            $table->foreign('employee_matricula')->references('matricula')->on('employees')->onDelete('cascade');

            // Evitar registros duplicados
            $table->unique(['event_id', 'employee_matricula']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_attendances');
    }
};
