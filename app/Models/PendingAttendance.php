<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'employee_matricula',
        'user_latitude',
        'user_longitude',
        'distance_meters',
        'verified',
        'checked_in_at',
        'migrated_to_attendance_id',
        'migrated_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'migrated_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Get the event that this pending attendance belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the employee that this pending attendance belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_matricula', 'matricula');
    }

    /**
     * Get the attendance record that this pending attendance was migrated to.
     */
    public function migratedToAttendance()
    {
        return $this->belongsTo(Attendance::class, 'migrated_to_attendance_id');
    }

    /**
     * Scope to get only non-migrated pending attendances.
     */
    public function scopeNotMigrated($query)
    {
        return $query->whereNull('migrated_to_attendance_id');
    }

    /**
     * Scope to get migrated pending attendances.
     */
    public function scopeMigrated($query)
    {
        return $query->whereNotNull('migrated_to_attendance_id');
    }

    /**
     * Check if this pending attendance has been migrated.
     */
    public function isMigrated(): bool
    {
        return !is_null($this->migrated_to_attendance_id);
    }
}
