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
        'role',            // <— if you created this column in migration
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
                    ->withTimestamps();
    }

    /**
     * Events created by this user (for coordinators).
     */
    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }
}
