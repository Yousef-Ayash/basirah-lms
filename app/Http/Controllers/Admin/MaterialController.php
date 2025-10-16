<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Jobs\GeneratePdfThumbnailJob;
use App\Models\Subject;
use App\Models\SubjectMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Services\YouTubeService;

class MaterialController extends Controller
{
    // POST admin/subjects/{subject}/materials
    public function store(StoreMaterialRequest $request, Subject $subject)
    {
        $type = $request->input('type');
        $data = $request->only(['title', 'key_points', 'type']);
        $data['subject_id'] = $subject->id;

        // if ($type === 'youtube') {
        //     $youtubeId = YouTubeService::extractYoutubeId($request->input('youtube_link'));
        //     $data['youtube_id'] = $youtubeId;
        //     $data['file_path'] = null;
        // } else {
        //     $file = $request->file('file');
        //     $ext = $file->getClientOriginalExtension();
        //     $basename = Str::uuid()->toString();
        //     $filename = "{$basename}.{$ext}";
        //     $path = $file->storeAs('materials', $filename, 'public');
        //     $data['file_path'] = $path;
        // }

        // $material = SubjectMaterial::create($data);

        // if ($type === 'picture' && $material->file_path) {
        //     // create thumbnail synchronously
        //     try {
        //         $disk = Storage::disk('public');
        //         $contents = $disk->get($material->file_path);
        //         $img = Image::make($contents);
        //         $img->orientate();
        //         $img->resize(800, null, function ($c) {
        //             $c->aspectRatio();
        //             $c->upsize();
        //         });
        //         $basename = pathinfo($material->file_path, PATHINFO_FILENAME);
        //         $thumbPath = "materials/thumbnails/{$basename}.jpg";
        //         $disk->put($thumbPath, (string) $img->encode('jpg', 80));
        //     } catch (\Throwable $e) {
        //         \Log::warning("Image thumbnail failed: " . $e->getMessage());
        //     }
        // }

        // if ($type === 'pdf' && $material->file_path) {
        //     GeneratePdfThumbnailJob::dispatch($material->id);
        // }

        // Handle YouTube link separately as it's not a file
        if ($type === 'youtube') {
            $youtubeId = YouTubeService::extractYoutubeId($request->input('youtube_link'));
            $data['youtube_id'] = $youtubeId;
        }

        // Create the database record for the material first.
        $material = SubjectMaterial::create($data);

        // If a file was uploaded (for 'pdf' or 'picture'), add it to the media library.
        if ($request->hasFile('file')) {
            $material->addMediaFromRequest('file')->toMediaCollection('attachments');
            $material = $material->fresh();
            $coverUrl = $material->file_url; // accessor
        }

        // return redirect()->route('admin.subjects.edit', $subject->id)->with('success', 'Material uploaded.');
        return redirect()->route('admin.subjects.edit', $subject->id)->with('success', __('messages.material_uploaded'));
    }

    public function destroy(SubjectMaterial $material)
    {
        // if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
        //     Storage::disk('public')->delete($material->file_path);
        // }

        // if ($material->file_path) {
        //     $basename = pathinfo($material->file_path, PATHINFO_FILENAME);
        //     $thumb = "materials/thumbnails/{$basename}.jpg";
        //     if (Storage::disk('public')->exists($thumb)) {
        //         Storage::disk('public')->delete($thumb);
        //     }
        // }

        // $subjectId = $material->subject_id;
        $material->delete();

        // return redirect()->back()->with('success', 'Material deleted.');
        return redirect()->back()->with('success', __('messages.material_deleted'));
    }
}
