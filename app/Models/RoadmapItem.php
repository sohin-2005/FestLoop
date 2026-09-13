<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapItem extends Model
{
    public const STATUSES = [
        'planned'     => 'Planned',
        'in_progress' => 'In the works',
        'done'        => 'Done',
    ];

    protected $fillable = ['club_id', 'title', 'description', 'target_label', 'status', 'sort_order'];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
