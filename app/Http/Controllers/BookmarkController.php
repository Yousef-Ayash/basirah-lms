<?php
namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\SubjectMaterial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookmarks = $user->bookmarks()->with(['material.subject'])->paginate(15);

        // transform for Inertia
        $items = $bookmarks->through(function ($b) {
            return [
                'id' => $b->id,
                'material' => [
                    'id' => $b->material->id,
                    'title' => $b->material->title,
                    'thumbnail_url' => $b->material->thumbnail_url,
                    'subject' => ['id' => $b->material->subject->id, 'title' => $b->material->subject->title],
                ],
                'created_at' => $b->created_at,
            ];
        });

        return Inertia::render('Bookmarks/Index', [
            'bookmarks' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['material_id' => 'required|exists:subject_materials,id']);

        $bookmark = Bookmark::firstOrCreate([
            'user_id' => auth()->id(),
            'material_id' => $request->material_id,
        ]);

        // return redirect()->back()->with('success', 'Bookmarked.');
        return redirect()->back()->with('success', __('messages.bookmarked'));
    }

    public function destroy(SubjectMaterial $material)
    {
        $user = auth()->user();

        $deleted = Bookmark::where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->delete();

        // return redirect()->back()->with('success', 'Bookmark removed.');
        return redirect()->back()->with('success', __('messages.bookmark_removed'));
    }
}
