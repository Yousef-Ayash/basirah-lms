<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\MarksReport;
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

        $last_attempt = StudentExamAttempt::where('exam_id', $exam->id)->where('user_id', $user->id)->latest('submitted_at')->value('submitted_at');

        if ($last_attempt) {
            $submittedDate = Carbon::parse($last_attempt)->startOfDay();
            $next_attempt_date = Carbon::parse($last_attempt)
                ->addDays($exam->distance_between_attempts)
                ->toDateString();
        }

        // $passedAttempts = StudentExamAttempt::where('user_id', $user->id)->where('exam_id', $exam->id)->where('mark', '>=', $exam->pass_threshold)->get()->count();

        $latestReport = MarksReport::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            // ->where('official', true)
            // ->latest('published_at', 'created_at')
            ->latest('created_at')
            ->first();

        // dd($officialReport);

        if ($latestReport) {
            $isPassed = ($latestReport->marks >= ($exam->pass_threshold ?? 50));
        } else {
            $passedAttempts = StudentExamAttempt::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->where('mark', '>=', $exam->pass_threshold)
                ->exists();
            $isPassed = $passedAttempts;
        }

        // Determine eligibility to start
        $now = Carbon::now();

        $canStart = $questionCount > 0
            && (!$exam->open_at || $now->gte($exam->open_at))
            && (!$exam->close_at || $now->lte($exam->close_at))
            && $attemptsCount < $exam->max_attempts
            && !$isPassed;

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
                'question_count' => $questionCount,
                'full_mark' => $exam->full_mark,
                'pass_threshold' => $exam->pass_threshold
            ],
            'attempts_count' => $attemptsCount,
            'can_start' => $canStart,
            'next_attempt_date' => $next_attempt_date ?? Carbon::parse(now())->toDateString(),
            'is_passed' => $isPassed
        ]);
    }
}
