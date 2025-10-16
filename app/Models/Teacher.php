<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia; // Import
use Spatie\MediaLibrary\InteractsWithMedia; // Import
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Import

class Teacher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'bio',
        // 'photo_path',
        // 'photo',
    ];

    // We can keep the photo_url accessor but change its logic
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'thumb');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    protected $appends = ['photo_url'];

    /**
     * Get the URL for the teacher's photo.
     */
    // public function getPhotoUrlAttribute(): ?string
    // {
    //     if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
    //         return Storage::disk('public')->url($this->photo_path);
    //     }
    //     // Return a default placeholder image if you have one
    //     return 'https://via.placeholder.com/400';
    // }

    /**
     * A teacher can have many subjects.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}