<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    public const REGISTERED = 'registered';
    public const PENDING = 'pending';
    public const WAITLISTED = 'waitlisted';
    public const REJECTED = 'rejected';

    /** Statuses that occupy one of the event's seats. */
    public const HOLDS_SEAT = [self::REGISTERED, self::PENDING];

    public const LABELS = [
        self::REGISTERED => "You're in",
        self::PENDING    => 'Awaiting approval',
        self::WAITLISTED => 'On the waitlist',
        self::REJECTED   => 'Not accepted',
    ];

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? ucfirst($this->status);
    }
}
