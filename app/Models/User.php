<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'year_of_study',
        'roll_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * A user can register for many events.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Events this user has registered for (through registrations table).
     */
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'registrations')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Clubs this student follows for updates.
     */
    public function followedClubs()
    {
        return $this->belongsToMany(Club::class, 'club_follows')->withTimestamps();
    }
}
