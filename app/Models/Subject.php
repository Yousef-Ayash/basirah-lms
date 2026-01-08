<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia; // Import the interface
use Spatie\MediaLibrary\InteractsWithMedia; // Import the trait
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Import for conversions

class Subject extends Model implements HasMedia // Implement the interface
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'teacher_id',
        'level_id',
        'course_id'
    ];

    // Optional: Add an accessor for easy access in your Vue components
    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'thumb'); // Get 'thumb' conversion
    }

    // Define your media conversions here
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(200)
            ->sharpen(10);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials()
    {
        return $this->hasMany(SubjectMaterial::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
