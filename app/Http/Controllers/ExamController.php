<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\StudentExamAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Show exam detail page for students.
     */
    public function show(Exam $exam)
    {
        $user = auth()->user();

        $questionCount = $exam->questions()->count();
        $attemptsCount = $user->attempts()->where('exam_id', $exam->id)->count();

        // Determine eligibility to start
        $now = Carbon::now();
        $canStart = $questionCount > 0 // <-- Add this condition
            && (!$exam->open_at || $now->gte($exam->open_at))
            && (!$exam->close_at || $now->lte($exam->close_at))
            && $attemptsCount < $exam->max_attempts;

        return Inertia::render('Exams/Show', [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'time_limit_minutes' => $exam->time_limit_minutes,
                'open_at' => $exam->open_at,
                'close_at' => $exam->close_at,
                'max_attempts' => $exam->max_attempts,
                'review_allowed' => $exam->review_allowed,
                // 3. Pass the question count to the frontend.
                'question_count' => $questionCount,
                'full_mark' => $exam->full_mark
            ],
            'attempts_count' => $attemptsCount,
            'can_start' => $canStart,
        ]);
    }
}
