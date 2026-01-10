<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    protected $appends = ['preview_url', 'thumbnail_url'];

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
        if ($this->type === 'youtube') return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        $media = $this->getFirstMedia('attachments');
        return $media ? route('media.secure', ['media' => $media->id, 'conversion' => 'thumb']) : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('attachments');
    }


    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type === 'youtube' && $this->youtube_id) {
            // Use privacy-enhanced domain and include common params:
            // - rel=0: don't show related videos from other channels
            // - modestbranding=1: minimal YouTube branding
            // - enablejsapi=1: allows player API if you later use JS control
            // - origin: required by YouTube for some embed checks (use app URL)
            $origin = config('app.url') ? urlencode(rtrim(config('app.url'), '/')) : null;
            $params = [
                'rel' => '0',
                'modestbranding' => '1',
                'enablejsapi' => '1',
            ];
            if ($origin) {
                $params['origin'] = $origin;
            }
            $query = http_build_query($params);

            // Use youtube-nocookie.com (privacy-enhanced) domain which avoids some cookie/referrer checks
            return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?{$query}";
        }

        // if ($this->type === 'picture' && $this->getFirstMediaUrl('attachments')) {
        //     return $this->getFirstMediaUrl('attachments');
        // }

        if ($this->type === 'picture') {
            // For private images, we use the secure route as the source
            return $this->preview_url;
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
