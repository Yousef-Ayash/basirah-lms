<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Services\YoutubeService;

class SubjectMaterial extends Model implements hasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'subject_materials';

    protected $fillable = [
        'subject_id',
        'title',
        'key_points',
        'type', // 'pdf','picture','youtube'
        'youtube_id',
        'order',
    ];

    protected $appends = ['preview_url', 'thumbnail_url', 'embed_url'];

    // Register the collection and force the 'private' disk
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('private') // Forces Spatie to use the secure disk
            ->singleFile();
    }

    // Generate thumbnails (these will also be stored on the private disk)
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued(); // Process immediately for testing
    }

    public function getPreviewUrlAttribute()
    {
        $media = $this->getFirstMedia('attachments');
        return $media ? route('media.secure', ['media' => $media->id]) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->type === 'youtube' && $this->youtube_id) {
            return YoutubeService::getThumbnail($this->youtube_id);
        }

        if ($this->type === 'youtube') return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        $media = $this->getFirstMedia('attachments');

        return $this->preview_url;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('attachments');
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type === 'youtube' && $this->youtube_id) {
            return YoutubeService::getEmbedUrl($this->youtube_id);
        }
        return null;
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'material_id');
    }
}
