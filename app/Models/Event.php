<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'allowed_radius',
        'start_time',
        'end_time',
        'active',
        'user_id',
        'qr_code',
    ];

     protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            // Solo generar QR code si no se ha proporcionado uno
            if (empty($event->qr_code)) {
                $event->qr_code = Str::uuid()->toString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'attendances')
                    ->withPivot('checked_in_at', 'verified', 'distance_meters');
    }

    public function isActive()
    {
        $now = now();
        return $this->active &&
               $now->gte($this->start_time) &&
               $now->lte($this->end_time);
    }

    // Nuevo accessor
    public function getAttendeesCountAttribute()
    {
        return $this->attendances()->count();
    }

    /**
     * Get the pending attendances for this event.
     */
    public function pendingAttendances()
    {
        return $this->hasMany(PendingAttendance::class);
    }

    /**
     * Get the event confirmations for this event.
     */
    public function confirmations()
    {
        return $this->hasMany(EventConfirmation::class);
    }

    /**
     * Get the total number of confirmed adults for this event.
     */
    public function getTotalAdultsAttribute()
    {
        return $this->confirmations()->sum('adults');
    }

    /**
     * Get the total number of confirmed teenagers for this event.
     */
    public function getTotalTeenagersAttribute()
    {
        return $this->confirmations()->sum('teenagers');
    }

    /**
     * Get the total number of confirmed children for this event.
     */
    public function getTotalChildrenAttribute()
    {
        return $this->confirmations()->sum('children');
    }

    /**
     * Get the total number of confirmed attendees (adults + teenagers + children).
     */
    public function getTotalConfirmedAttendeesAttribute()
    {
        return $this->total_adults + $this->total_teenagers + $this->total_children;
    }
}
