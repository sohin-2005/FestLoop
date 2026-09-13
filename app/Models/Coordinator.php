<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Coordinator extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'club_id', 'position',
    ];

    protected $hidden = [
        'password',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
