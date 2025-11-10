<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
        // 'file_path',
        'youtube_id',
        'order',
    ];

    // protected $appends = ['preview_url', 'thumbnail_url'];
    protected $appends = ['preview_url', 'thumbnail_url', 'embed_url'];

    public function getPreviewUrlAttribute()
    {
        if ($this->type === 'youtube' && $this->youtube_id) {
            return "https://www.youtube.com/watch?v={$this->youtube_id}";
        }

        // For files, rely on media library
        return $this->getFirstMediaUrl('attachments'); // returns full URL or '' if none
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->type === 'youtube' && $this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }
        return $this->getFirstMediaUrl('attachments', 'thumb'); // conversion
    }


    // We can now remove the getPreviewUrlAttribute and getThumbnailUrlAttribute
    // and instead create new ones that use the media library.
    public function getFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('attachments');
    }

    // public function getEmbedUrlAttribute(): ?string
    // {
    //     if ($this->type === 'youtube' && $this->youtube_id) {
    //         return "https://www.youtube.com/embed/{$this->youtube_id}";
    //     }
    //     if ($this->type === 'picture' && $this->file_path) {
    //         return $this->getFileUrlAttribute(); // Use the new accessor
    //     }
    //     return null;
    // }

    // app/Models/SubjectMaterial.php
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

        if ($this->type === 'picture' && $this->getFirstMediaUrl('attachments')) {
            return $this->getFirstMediaUrl('attachments');
        }

        return null;
    }


    public function registerMediaConversions(Media $media = null): void
    {
        // For PDFs, it will generate a thumbnail from the first page.
        // For images, it will resize them.
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'material_id');
    }

    // preview url: for files -> Storage::url(file_path), for youtube -> embed link
    // public function getPreviewUrlAttribute()
    // {
    //     if ($this->type === 'youtube' && $this->youtube_id) {
    //         return "https://www.youtube.com/watch?v={$this->youtube_id}";
    //     }

    //     if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
    //         return Storage::disk('public')->url($this->file_path);
    //     }

    //     return null;
    // }

    // thumbnail url: images and generated pdf thumbnails, youtube thumbnail via youtube service
    // public function getThumbnailUrlAttribute()
    // {
    //     if ($this->type === 'youtube' && $this->youtube_id) {
    //         return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    //     }

    //     // expected thumbnail path convention: materials/thumbnails/{basename}.jpg
    //     if ($this->file_path) {
    //         $basename = pathinfo($this->file_path, PATHINFO_FILENAME);
    //         $thumbPath = "materials/thumbnails/{$basename}.jpg";
    //         if (Storage::disk('public')->exists($thumbPath)) {
    //             return Storage::disk('public')->url($thumbPath);
    //         }
    //     }

    //     return null;
    // }

    // // Add this new accessor method
    // public function getEmbedUrlAttribute(): ?string
    // {
    //     if ($this->type === 'youtube' && $this->youtube_id) {
    //         return "https://www.youtube.com/embed/{$this->youtube_id}";
    //     }

    //     // For pictures, the embed url is the same as the preview url
    //     if ($this->type === 'picture' && $this->file_path) {
    //         return $this->getPreviewUrlAttribute();
    //     }

    //     return null;
    // }
}
