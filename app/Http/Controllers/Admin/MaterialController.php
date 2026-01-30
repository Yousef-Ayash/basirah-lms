<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Models\Subject;
use App\Models\SubjectMaterial;
use App\Services\YoutubeService;
use Illuminate\Http\RedirectResponse;

class MaterialController extends Controller
{
    /**
     * Store a new subject material (YouTube link or File).
     * * @param StoreMaterialRequest $request
     * @param Subject $subject
     * @return RedirectResponse
     */
    public function store(StoreMaterialRequest $request, Subject $subject): RedirectResponse
    {
        // Get validated data from the Request class
        $data = $request->validated();

        // If type is YouTube, extract the ID using the Service logic
        if ($data['type'] === 'youtube') {
            // We use the 'youtube_link' input to populate the 'youtube_id' column
            $data['youtube_id'] = YoutubeService::extractId($request->input('youtube_link'));
        }

        // Create the record via the relationship
        $material = $subject->materials()->create($data);

        // Handle Spatie Media Library upload if a file exists
        // Spatie handles the storage disk and directory based on the Model configuration
        if ($request->hasFile('file')) {
            $material->addMediaFromRequest('file')
                ->toMediaCollection('attachments');
        }

        return redirect()->route('admin.subjects.edit', $subject->id)
            ->with('success', __('messages.material_uploaded'));
    }

    /**
     * Remove the specified material from storage.
     * * @param SubjectMaterial $material
     * @return RedirectResponse
     */
    public function destroy(SubjectMaterial $material): RedirectResponse
    {
        /**
         * Note: Spatie Media Library automatically deletes associated 
         * files and thumbnails when the model is deleted, provided the 
         * InteractsWithMedia trait is used in the model.
         */
        $material->delete();

        return redirect()->back()->with('success', __('messages.material_deleted'));
    }
}
