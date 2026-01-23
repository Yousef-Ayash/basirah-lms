<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\SubjectMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Jobs\GeneratePdfThumbnailJob;
use Inertia\Inertia;

class MaterialController extends Controller
{
    // store material (admin)
    public function store(StoreMaterialRequest $request)
    {
        $data = $request->only(['subject_id', 'title', 'key_points', 'type']);
        $type = $data['type'];

        if ($type === 'youtube') {
            $youtubeId = self::extractYoutubeId($request->input('youtube_link'));
            $data['youtube_id'] = $youtubeId;
            $data['file_path'] = null;
        } else {
            // store uploaded file under storage/app/public/materials
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $basename = Str::uuid()->toString();
            $filename = "{$basename}.{$ext}";
            $path = $file->storeAs('materials', $filename, 'public'); // returns path relative to disk root
            $data['file_path'] = $path;
        }

        $material = SubjectMaterial::create($data);

        // generate thumbnails
        if ($type === 'picture' && $material->file_path) {
            // synchronous thumbnail creation for images
            $this->makeImageThumbnail($material->file_path);
        }

        if ($type === 'pdf' && $material->file_path) {
            // dispatch queued job to create pdf thumbnail
            GeneratePdfThumbnailJob::dispatch($material->id);
        }

        return response()->json([
            'success' => true,
            'material' => $material->fresh(), // includes preview_url & thumbnail_url accessors
        ], 201);
    }

    public function show(SubjectMaterial $material)
    {
        $material->load('subject:id,title');

        return Inertia::render('Shared/MaterialViewer', [
            'material' => [
                'id' => $material->id,
                'title' => $material->title,
                'type' => $material->type,
                'key_points' => $material->key_points,
                'preview_url' => $material->preview_url,
                'thumbnail_url' => $material->thumbnail_url,
                'embed_url' => $material->embed_url,
                'subject' => $material->subject,
            ],
        ]);
    }

    public function destroy(SubjectMaterial $material)
    {
        // delete files (file and thumbnail) if exist
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $basename = $material->file_path ? pathinfo($material->file_path, PATHINFO_FILENAME) : null;
        if ($basename) {
            $thumb = "materials/thumbnails/{$basename}.jpg";
            if (Storage::disk('public')->exists($thumb)) {
                Storage::disk('public')->delete($thumb);
            }
        }

        $material->delete();

        return response()->json(['success' => true]);
    }

    // helper: create image thumbnail (synchronous)
    protected function makeImageThumbnail(string $filePath)
    {
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists($filePath))
                return false;

            $contents = $disk->get($filePath);
            $img = Image::make($contents);

            // target size (max width 1200 -> reduce large images; create two sizes optionally)
            $img->orientate();

            // create main thumbnail: 800x600 (keep aspect ratio)
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $basename = pathinfo($filePath, PATHINFO_FILENAME);
            $thumbPath = "materials/thumbnails/{$basename}.jpg";

            // save to temporary stream then put to storage so Intervention doesn't need filesystem write perms
            $disk->put($thumbPath, (string) $img->encode('jpg', 80));

            return true;
        } catch (\Throwable $e) {
            \Log::error('Image thumbnail error: ' . $e->getMessage());
            return false;
        }
    }
}
