<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Mockery\Matcher\Subset;

class SubjectController extends Controller
{
    // GET /subjects
    public function index(Request $request)
    {
        $user = $request->user();
        $studentLevelId = $user->level_id ?? 1;

        // Filters
        $courseId = $request->input('course_id');
        $levelId = $request->input('level_id');
        $q = $request->input('q');

        // Start Query
        $query = Subject::query()
            ->where('level_id', '<=', $studentLevelId)
            ->with(['teacher', 'course'])
            ->withCount(['materials', 'exams']);

        // Apply Course Filter
        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($levelId) {
            $query->where('level_id', $levelId);
        }

        // Apply Search
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $subjects = $query
            ->with(['level:id,name', 'course:id,title', 'teacher'])
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $levels = Level::where('id', '<=', $studentLevelId)->orderBy('order')->get();

        // If a course is selected, fetch its details to show a title/header in the view
        $selectedCourse = $courseId ? Course::find($courseId) : null;

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
            'levels' => $levels,
            'filters' => [
                'q' => $q,
                'course_id' => $courseId,
                'level_id' => $levelId
            ],
            'selectedCourse' => $selectedCourse
        ]);
    }

    // GET /subjects/{subject}
    public function show(Subject $subject, Request $request)
    {
        // eager load materials (paginated) and exams
        $materials = $subject->materials()
            // CORRECT: use whereIn for multiple possible values
            ->whereIn('type', ['pdf', 'picture'])
            ->orderBy('order')
            ->paginate(12)
            // avoid N+1 when accessors use media library: load media for each item
            ->through(function ($m) {
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'type' => $m->type,
                    'preview_url' => $m->preview_url,
                    'thumbnail_url' => $m->thumbnail_url,
                    'order' => $m->order,
                ];
            });

        $vid_materials = $subject->materials()
            ->where('type', 'youtube')
            ->orderBy('order')
            ->paginate(12)
            ->through(function ($m) {
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'type' => $m->type,
                    'preview_url' => $m->preview_url,
                    'thumbnail_url' => $m->thumbnail_url,
                    'order' => $m->order,
                ];
            });

        $teacher = Teacher::firstWhere('id', $subject->teacher_id);

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
                'cover_image' => $subject->cover_url,
            ],
            'materials' => $materials,
            'vid_materials' => $vid_materials,
            'exams' => $exams,
            'teacher' => $teacher,
            'bookmarkedMaterialIds' => $bookmarkedMaterialIds,
        ]);
    }
}
