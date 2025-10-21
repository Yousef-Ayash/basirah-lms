<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\SubjectMaterial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Check if the user has the 'admin' role
        if ($user->hasRole('admin')) {
            // Fetch data needed for the Admin Dashboard
            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'studentsCount' => User::role('student')->count(),
                    'subjectsCount' => Subject::count(),
                    'pendingCount' => User::role('student')->where('is_approved', false)->count(),
                ],
                'recentStudents' => User::role('student')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(['id', 'name', 'email', 'is_approved']),
            ]);
        }

        // Check if the user has the 'student' role
        if ($user->hasRole('student')) {
            // --- Summary Card Queries ---
            $enrolledSubjects = Subject::where('level_id', $user->level_id)->count();
            $examsTaken = $user->attempts()->distinct()->count('exam_id');
            $averageScore = round($user->attempts()->whereNotNull('score')->avg('score') ?? 0);

            // --- Upcoming Exams Query ---
            $now = Carbon::now();
            $attemptedExamIds = $user->attempts()
                ->select('exam_id', DB::raw('count(*) as attempts_count'))
                ->groupBy('exam_id')
                ->get()
                ->keyBy('exam_id');

            $upcomingExams = Exam::with('subject')
                ->whereHas('subject', fn($q) => $q->where('level_id', $user->level_id))
                ->where(function ($query) use ($now) {
                    $query->where('close_at', '>=', $now)
                        ->orWhereNull('close_at');
                })
                ->get()
                ->filter(function ($exam) use ($attemptedExamIds) {
                    $attemptsCount = $attemptedExamIds->get($exam->id)?->attempts_count ?? 0;
                    return $attemptsCount < $exam->max_attempts;
                })
                ->take(5);

            // --- Recent Materials Query ---
            $recentMaterials = SubjectMaterial::with('subject')
                ->whereHas('subject', fn($q) => $q->where('level_id', $user->level_id))
                ->latest() // Orders by created_at descending
                ->take(5)
                ->get();

            return Inertia::render('Student/Dashboard', [
                'summary' => [
                    'enrolledSubjects' => $enrolledSubjects,
                    'examsTaken' => $examsTaken,
                    'averageScore' => $averageScore,
                ],
                'upcomingExams' => $upcomingExams,
                'recentMaterials' => $recentMaterials,
            ]);
        }

        // Fallback for any other roles or if something goes wrong
        return redirect('/');
    }
}