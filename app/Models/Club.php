<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Club extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'technical'    => 'Technical',
        'cultural'     => 'Cultural',
        'arts'         => 'Arts & Media',
        'sports'       => 'Sports',
        'social'       => 'Social Impact',
        'professional' => 'Professional',
    ];

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'tagline',
        'category',
        'about',
        'mission',
        'logo_path',
        'cover_path',
        'accent_color',
        'founded_year',
        'email',
        'website',
        'instagram',
        'linkedin',
        'faculty_advisor',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Club $club) {
            if (blank($club->slug)) {
                $base = Str::slug($club->name) ?: 'club';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $club->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coordinators()
    {
        return $this->hasMany(Coordinator::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class)->orderByDesc('achieved_on');
    }

    public function roadmapItems()
    {
        return $this->hasMany(RoadmapItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class)->orderByDesc('is_pinned')->latest();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'club_follows')->withTimestamps();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isFollowedBy(?User $user): bool
    {
        return $user !== null && $this->followers()->whereKey($user->id)->exists();
    }

    public function getInitialsAttribute(): string
    {
        if ($this->short_name) {
            return $this->short_name;
        }

        return collect(preg_split('/\s+/', $this->name))
            ->reject(fn ($word) => in_array(strtolower($word), ['the', 'of', 'and', '&']))
            ->take(2)
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::headline($this->category);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
    }
}
