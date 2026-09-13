<?php

namespace App\Models;

use App\Notifications\RegistrationStatusChanged;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'hackathon'   => 'Hackathon',
        'workshop'    => 'Workshop',
        'talk'        => 'Talk',
        'competition' => 'Competition',
        'cultural'    => 'Cultural',
        'sports'      => 'Sports',
        'social'      => 'Meetup',
        'technical'   => 'Technical',
    ];

    public const MODES = [
        'offline' => 'On campus',
        'online'  => 'Online',
        'hybrid'  => 'Hybrid',
    ];

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
        'contact_email',
        'contact_phone',
        'rules',
        'user_id',
        'coordinator_id',
        'club_id',
        'mode',
        'external_registration_url',
        'recap',
    ];

    protected $casts = [
        'start_time'            => 'datetime',
        'end_time'              => 'datetime',
        'registration_deadline' => 'datetime',
        'requires_approval'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistrations()
    {
        return $this->hasMany(Registration::class)
            ->whereIn('status', Registration::HOLDS_SEAT);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'registrations')->withPivot('status')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Only events whose club has been approved by the admin are public. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereHas('club', fn ($q) => $q->approved());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_time', '>', now());
    }

    public function scopeOngoing(Builder $query): Builder
    {
        $now = now();

        return $query->where('start_time', '<=', $now)
            ->where(fn ($q) => $q->where('end_time', '>=', $now)
                ->orWhere(fn ($q2) => $q2->whereNull('end_time')->where('start_time', '>=', $now->copy()->subHours(3))));
    }

    public function scopePast(Builder $query): Builder
    {
        $now = now();

        return $query->where(fn ($q) => $q->where('end_time', '<', $now)
            ->orWhere(fn ($q2) => $q2->whereNull('end_time')->where('start_time', '<', $now->copy()->subHours(3))));
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('club', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        if ($club = $filters['club'] ?? null) {
            $query->whereHas('club', fn ($c) => $c->where('slug', $club));
        }

        match ($filters['time'] ?? null) {
            'upcoming' => $query->upcoming()->orderBy('start_time'),
            'ongoing'  => $query->ongoing()->orderBy('start_time'),
            'past'     => $query->past()->orderByDesc('start_time'),
            'week'     => $query->whereBetween('start_time', [now(), now()->endOfWeek()])->orderBy('start_time'),
            default    => $query->where(fn ($q) => $q->upcoming()->orWhere(fn ($q2) => $q2->ongoing()))->orderBy('start_time'),
        };

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | State helpers
    |--------------------------------------------------------------------------
    */

    public function isOngoing(): bool
    {
        $end = $this->end_time ?? $this->start_time->copy()->addHours(3);

        return now()->between($this->start_time, $end);
    }

    public function isPast(): bool
    {
        $end = $this->end_time ?? $this->start_time->copy()->addHours(3);

        return now()->gt($end);
    }

    public function isUpcoming(): bool
    {
        return now()->lt($this->start_time);
    }

    public function usesExternalRegistration(): bool
    {
        return filled($this->external_registration_url);
    }

    public function registrationOpen(): bool
    {
        if ($this->isPast()) {
            return false;
        }

        if ($this->registration_deadline && now()->gt($this->registration_deadline)) {
            return false;
        }

        return true;
    }

    public function seatsTaken(): int
    {
        return $this->active_registrations_count ?? $this->activeRegistrations()->count();
    }

    public function spotsLeft(): ?int
    {
        if (! $this->max_participants) {
            return null;
        }

        return max(0, $this->max_participants - $this->seatsTaken());
    }

    public function isFull(): bool
    {
        return $this->spotsLeft() === 0;
    }

    public function registrationFor(?User $user): ?Registration
    {
        if (! $user) {
            return null;
        }

        return $this->registrations()->where('user_id', $user->id)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Registration flow
    |--------------------------------------------------------------------------
    */

    /**
     * Register a student. Seats go to "registered" (or "pending" when the club
     * reviews sign-ups); once capacity is reached new sign-ups join the waitlist.
     */
    public function registerUser(User $user): Registration
    {
        return DB::transaction(function () use ($user) {
            $existing = $this->registrations()->where('user_id', $user->id)->lockForUpdate()->first();

            if ($existing && $existing->status !== Registration::REJECTED) {
                return $existing;
            }

            $status = match (true) {
                $this->isFull()          => Registration::WAITLISTED,
                $this->requires_approval => Registration::PENDING,
                default                  => Registration::REGISTERED,
            };

            if ($existing) {
                $existing->update(['status' => $status]);

                return $existing;
            }

            return $this->registrations()->create([
                'user_id' => $user->id,
                'status'  => $status,
            ]);
        });
    }

    /** Cancel a student's spot and hand it to the first person on the waitlist. */
    public function cancelRegistration(User $user): void
    {
        DB::transaction(function () use ($user) {
            $registration = $this->registrations()->where('user_id', $user->id)->first();

            if (! $registration) {
                return;
            }

            $freedSeat = in_array($registration->status, Registration::HOLDS_SEAT, true);
            $registration->delete();

            if ($freedSeat) {
                $this->promoteFromWaitlist();
            }
        });
    }

    public function promoteFromWaitlist(): void
    {
        if ($this->isFull()) {
            return;
        }

        $next = $this->registrations()
            ->where('status', Registration::WAITLISTED)
            ->oldest()
            ->first();

        if (! $next) {
            return;
        }

        $next->update([
            'status' => $this->requires_approval ? Registration::PENDING : Registration::REGISTERED,
        ]);

        $next->user->notify(new RegistrationStatusChanged($next));
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_image ? asset('storage/'.$this->banner_image) : null;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::headline($this->category ?? 'Event');
    }

    public function getModeLabelAttribute(): string
    {
        return self::MODES[$this->mode] ?? 'On campus';
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags((string) $this->description), 140);
    }
}
