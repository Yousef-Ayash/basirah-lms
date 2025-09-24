<?php
namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    // GET /subjects
    public function index(Request $request)
    {
        // 1. Get the currently authenticated user
        $user = $request->user();

        // 2. Determine the student's current level. Default to level 1 if not set.
        $studentLevelId = $user->level_id ?? 1;

        // 3. Start the query for Subjects
        $query = Subject::query()->withCount(['materials', 'exams'])->with('level');

        // 4. IMPORTANT: Filter subjects to only include those at or below the student's level
        $query->whereHas('level', function ($q) use ($studentLevelId) {
            $q->where('id', '<=', $studentLevelId);
        });

        // --- The existing filters still work on top of the personalized list ---
        $q = $request->input('q');
        $levelId = $request->input('level_id');

        if ($levelId) {
            $query->where('level_id', $levelId);
        }

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $subjects = $query->orderBy('title')->paginate(12)->withQueryString();

        // 5. IMPORTANT: Also filter the list of Levels for the dropdown
        //    so a student can't filter by levels they haven't reached yet.
        $levels = Level::where('id', '<=', $studentLevelId)->orderBy('order')->get();

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
            'levels' => $levels,
            'filters' => ['q' => $q, 'level_id' => $levelId],
        ]);
    }

    // GET /subjects/{subject}
    public function show(Subject $subject, Request $request)
    {
        // eager load materials (paginated) and exams
        $materials = $subject->materials()
            ->orderBy('order')
            ->paginate(12)
            ->through(function ($m) {
                // deja vu: accessor provides preview_url / thumbnail_url
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'type' => $m->type,
                    'preview_url' => $m->preview_url,
                    'thumbnail_url' => $m->thumbnail_url,
                    'order' => $m->order,
                ];
            });

        $exams = $subject->exams()
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'time_limit_minutes', 'open_at', 'close_at']);

        // provide bookmark map for current user (so frontend can show bookmarked state)
        $bookmarkedMaterialIds = auth()->user()->bookmarks()->pluck('material_id')->all();

        return Inertia::render('Subjects/Show', [
            'subject' => [
                'id' => $subject->id,
                'title' => $subject->title,
                'description' => $subject->description,
                'cover_image' => $subject->cover_image_path ? \Storage::disk('public')->url($subject->cover_image_path) : null,
            ],
            'materials' => $materials,
            'exams' => $exams,
            'bookmarkedMaterialIds' => $bookmarkedMaterialIds,
        ]);
    }
}
