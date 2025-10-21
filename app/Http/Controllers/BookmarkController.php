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
            $m = $b->material;
            return [
                'id' => $b->id,
                'material' => [
                    'id' => $m->id,
                    'title' => $m->title,
                    'preview_url' => $m->preview_url,       // <<-- add preview_url
                    'thumbnail_url' => $m->thumbnail_url,
                    'type' => $m->type,
                    'subject' => [
                        'id' => $m->subject->id,
                        'title' => $m->subject->title
                    ],
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
        return redirect()->back()->with('success', 'تمت الإشارة المرجعية.');
    }

    public function destroy(SubjectMaterial $material)
    {
        $user = auth()->user();

        $deleted = Bookmark::where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->delete();

        // return redirect()->back()->with('success', 'Bookmark removed.');
        return redirect()->back()->with('success', 'تمت إزالة الإشارة المرجعية.');
    }
}
