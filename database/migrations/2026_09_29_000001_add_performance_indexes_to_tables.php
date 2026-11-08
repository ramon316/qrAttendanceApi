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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['user_id', 'checked_in_at'], 'idx_attendances_user_checked_in');
            $table->index(['event_id', 'checked_in_at'], 'idx_attendances_event_checked_in');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['active', 'start_time', 'end_time'], 'idx_events_active_dates');
        });

        Schema::table('pending_attendances', function (Blueprint $table) {
            $table->index(['employee_matricula', 'migrated_to_attendance_id'], 'idx_pending_matricula_migrated');
            $table->index(['migrated_to_attendance_id'], 'idx_pending_migrated_id');
        });

        Schema::table('event_confirmations', function (Blueprint $table) {
            $table->index(['event_id', 'zone'], 'idx_confirmations_event_zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_user_checked_in');
            $table->dropIndex('idx_attendances_event_checked_in');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_active_dates');
        });

        Schema::table('pending_attendances', function (Blueprint $table) {
            $table->dropIndex('idx_pending_matricula_migrated');
            $table->dropIndex('idx_pending_migrated_id');
        });

        Schema::table('event_confirmations', function (Blueprint $table) {
            $table->dropIndex('idx_confirmations_event_zone');
        });
    }
};
