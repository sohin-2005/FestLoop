<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['club_id', 'title', 'body', 'is_pinned'];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
