<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'photo_path',
    ];

    protected $appends = ['photo_url'];

    /**
     * Get the URL for the teacher's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::disk('public')->url($this->photo_path);
        }
        // Return a default placeholder image if you have one
        return 'https://via.placeholder.com/400';
    }

    /**
     * A teacher can have many subjects.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}