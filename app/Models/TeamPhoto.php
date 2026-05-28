<?php
// app/Models/TeamPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamPhoto extends Model
{
    protected $table = 'team_photos';

    protected $fillable = [
        'team_id', 'photo_path', 'title', 'description', 'stage_number', 'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getPhotoUrlAttribute()
    {
        return Storage::url($this->photo_path);
    }
}
