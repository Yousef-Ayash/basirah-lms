<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of courses (replacing the old subject list).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentLevelId = $user->level_id ?? 1;
        $q = $request->input('q');

        // Only show courses that contain subjects relevant to the student's level (or below)
        $query = Course::query()
            ->withCount(['subjects' => function ($query) use ($studentLevelId) {
                $query->where('level_id', '<=', $studentLevelId);
            }])
            ->whereHas('subjects', function ($query) use ($studentLevelId) {
                $query->where('level_id', '<=', $studentLevelId);
            });

        if ($q) {
            $query->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        }

        $courses = $query->orderBy('order')
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'filters' => ['q' => $q],
        ]);
    }

    /**
     * Display a specific course and list its subjects.
     */
    public function show(Course $course, Request $request)
    {
        $user = $request->user();
        $studentLevelId = $user->level_id ?? 1;

        // Fetch subjects belonging to this course, filtered by student level
        $subjects = $course->subjects()
            ->where('level_id', '<=', $studentLevelId)
            ->withCount(['materials', 'exams'])
            ->with('teacher') // Eager load teacher for the card display
            ->orderBy('title')
            ->get();

        return Inertia::render('Courses/Show', [
            'course' => $course->append('cover_url'),
            'subjects' => $subjects,
        ]);
    }
}
