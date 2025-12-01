<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'zone',
        'adults',
        'teenagers',
        'children',
    ];

    protected $casts = [
        'adults' => 'integer',
        'teenagers' => 'integer',
        'children' => 'integer',
    ];

    /**
     * Get the user that owns the confirmation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that this confirmation belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the total number of attendees (adults + teenagers + children).
     */
    public function getTotalAttendeesAttribute()
    {
        return $this->adults + $this->teenagers + $this->children;
    }
}
