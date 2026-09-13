<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['club_id', 'title', 'description', 'achieved_on'];

    protected $casts = [
        'achieved_on' => 'date',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
