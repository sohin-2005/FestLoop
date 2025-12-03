<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'start_time',
        'end_time',
        'banner_image',
        'category',
        'venue_details',
        'max_participants',
        'registration_deadline',
        'requires_approval',
        'organizer',
        'contact_email',
        'contact_phone',
        'rules',
    ];

    protected $casts = [
        'start_time'           => 'datetime',
        'end_time'             => 'datetime',
        'registration_deadline'=> 'datetime',
        'requires_approval'    => 'boolean',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'registrations');
    }
}
