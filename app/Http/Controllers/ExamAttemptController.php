<?php
namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\StudentExamAttempt;
use App\Models\StudentExamAnswer;
use App\Models\ExamLog;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Exports\MyMarksExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ExamAttemptController extends Controller
{
    // POST /exams/{exam}/start
    public function start(Request $request, Exam $exam)
    {
        $user = $request->user();
        $now = now();

        // check open/close
        if ($exam->open_at && $now->lt($exam->open_at)) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.exam_not_open_yet')]);
        }
        if ($exam->close_at && $now->gt($exam->close_at)) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.exam_is_closed')]);
        }

        // attempts check
        $attemptCount = StudentExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.max_attempts_reached')]);
        }

        if ($exam->questions()->count() === 0) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.exam_no_questions')]);
        }

        $questionsQuery = $exam->questions();

        // Check if we need to select a random subset of questions
        if ($exam->questions_to_display > 0 && $questionsQuery->count() >= $exam->questions_to_display) {
            // Fetch a random subset of questions directly from the database
            $questions = $questionsQuery->inRandomOrder()->limit($exam->questions_to_display)->get();
        } else {
            // Fallback to the original behavior: get all questions in order
            $questions = $questionsQuery->orderBy('order')->get();
        }

        // Pluck the IDs to store them, ensuring the order is preserved for this attempt
        $questionIds = $questions->pluck('id')->toArray();

        // create attempt
        $attempt = StudentExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'started_at' => $now,
            'attempt_number' => $attemptCount + 1,
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'question_ids' => $questionIds
            ],
        ]);

        // log
        ExamLog::create([
            'exam_attempt_id' => $attempt->id,
            'user_id' => $user->id,
            'action' => 'start',
            'metadata' => ['attempt_number' => $attempt->attempt_number],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // load questions without correct answers
        // $questions = $exam->questions()->orderBy('order')->get()->map(function (ExamQuestion $q) {
        //     return [
        //         'id' => $q->id,
        //         'question_text' => $q->question_text,
        //         'options' => $q->options,
        //         'order' => $q->order,
        //     ];
        // });

        $displayQuestions = $questions->map(function (ExamQuestion $q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
            ];
        });

        // compute time_left (seconds) if time_limit_minutes present
        $timeLimitSeconds = $exam->time_limit_minutes ? $exam->time_limit_minutes * 60 : null;
        $expiresAt = $timeLimitSeconds ? $now->copy()->addSeconds($timeLimitSeconds) : null;

        return Inertia::render('Exams/Take', [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'time_limit_minutes' => $exam->time_limit_minutes,
            ],
            'attempt' => [
                'id' => $attempt->id,
                'started_at' => $attempt->started_at,
                'expires_at' => $expiresAt,
            ],
            'questions' => $displayQuestions,
        ]);
    }

    // GET attempts list for user
    public function index(Request $request)
    {
        $attempts = StudentExamAttempt::with('exam')->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Exams/MyAttempts', ['attempts' => $attempts]);
    }

    // GET /attempts/{attempt}
    public function show(Request $request, StudentExamAttempt $attempt)
    {
        $user = $request->user();
        if ($attempt->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403);
        }

        // if not submitted, show attempt-in-progress page
        $exam = $attempt->exam()->first();
        $questions = $attempt->exam->questions()->orderBy('order')->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'order' => $q->order,
            ];
        });

        $answers = $attempt->answers()->pluck('selected_option', 'question_id')->toArray();

        // return Inertia::render('Exams/AttemptShow', [
        return Inertia::render('Attempts/Show', [
            'attempt' => $attempt,
            'exam' => $exam,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $fileName = 'my-marks-' . now()->format('Ymd') . '.xlsx';

        // The export is always scoped to the authenticated user
        return Excel::download(new MyMarksExport($user), $fileName);
    }

    public function download(Request $request, StudentExamAttempt $attempt)
    {
        // Authorize: Ensure the user owns the attempt or is an admin
        $user = $request->user();
        if ($attempt->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403);
        }

        // Fetch all the same data as the show() method
        $exam = $attempt->exam;
        $questions = $exam->questions()->orderBy('order')->get();
        $answers = $attempt->answers()->pluck('selected_option', 'question_id')->toArray();
        $canReview = $exam->review_allowed;
        $showAnswers = $exam->show_answers_after_close && $exam->close_at && now()->gt($exam->close_at);

        // Load the Blade view with the data
        $pdf = PDF::loadView('pdfs.exam_result', [
            'attempt' => $attempt->load('user'),
            'exam' => $exam,
            'questions' => $questions,
            'answers' => $answers,
            'canReview' => $canReview,
            'showAnswers' => $showAnswers,
        ]);

        $fileName = 'exam-result-' . Str::slug($exam->title) . '-' . $attempt->id . '.pdf';

        // Stream the PDF to the browser for download
        return $pdf->stream($fileName);
    }

    // POST /attempts/{attempt}/autosave
    public function autosave(Request $request, StudentExamAttempt $attempt)
    {
        $user = $request->user();
        if ($attempt->user_id !== $user->id)
            abort(403);
        if ($attempt->submitted_at)
            return response()->json(['error' => 'Attempt already submitted.'], 422);

        $data = $request->validate(['answers' => 'required|array']);

        $answers = $data['answers']; // expect [question_id => selected_option]

        foreach ($answers as $qid => $sel) {
            StudentExamAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qid],
                ['selected_option' => $sel, 'is_correct' => null]
            );
        }

        // log autosave
        ExamLog::create([
            'exam_attempt_id' => $attempt->id,
            'user_id' => $user->id,
            'action' => 'autosave',
            'metadata' => ['count' => count($answers)],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['success' => true]);
    }

    // POST /attempts/{attempt}/submit
    public function submit(Request $request, StudentExamAttempt $attempt)
    {
        $user = $request->user();
        $exam = $attempt->exam()->first();

        if ($attempt->user_id !== $user->id) {
            abort(403);
        }
        if ($attempt->submitted_at) {
            // return redirect()->route('exams.show', $attempt->exam()->first())->withErrors(['attempt' => 'Attempt already submitted.']);
            return redirect()->route('exams.show', $exam)->withErrors(['attempt' => __('messages.attempt_already_submitted')]);
        }

        $now = now();

        // re-check attempt count and open/close windows
        $attemptCount = StudentExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attempt->attempt_number > $exam->max_attempts) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Attempt exceeded allowed attempts.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.max_attempts_reached')]);
        }

        if ($exam->open_at && $now->lt($exam->open_at)) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Exam not open.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.exam_not_open_yet')]);
        }

        if ($exam->close_at && $now->gt($exam->close_at)) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Exam closed.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => __('messages.exam_is_closed')]);
        }

        // time limit check if exists
        if ($exam->time_limit_minutes) {
            $expires = $attempt->started_at->copy()->addMinutes($exam->time_limit_minutes);
            if ($now->gt($expires)) {
                // return redirect()->route('exams.show', $exam)->withErrors(['time' => 'Time limit exceeded.']);
                return redirect()->route('exams.show', $exam)->withErrors(['time' => __('messages.time_limit_exceeded')]);
            }
        }

        // MAKE SURE THIS IS NULLABLE OR ELSE THE SUBMIT WILL BREAK;
        $data = $request->validate(['answers' => 'nullable|array']);

        $answersPayload = $data['answers'];

        DB::beginTransaction();
        try {

            // Get the specific question IDs that were part of this attempt from metadata
            $questionIdsForThisAttempt = $attempt->metadata['question_ids'] ?? null;

            if ($questionIdsForThisAttempt) {
                // If specific questions were stored, fetch only those
                $questions = \App\Models\ExamQuestion::whereIn('id', $questionIdsForThisAttempt)->get()->keyBy('id');
            } else {
                // Fallback for older attempts: get all questions for the exam
                $questions = $exam->questions()->get()->keyBy('id');
            }
            // $questions = $exam->questions()->get()->keyBy('id');

            $total = $questions->count();
            $correct = 0;

            foreach ($answersPayload as $qid => $sel) {
                if (!isset($questions[$qid]))
                    continue;
                $q = $questions[$qid];
                $isCorrect = ((int) $sel === (int) $q->correct_answer);
                if ($isCorrect)
                    $correct++;
                StudentExamAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $qid],
                    ['selected_option' => $sel, 'is_correct' => $isCorrect]
                );
            }

            $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0.0;

            $attempt->submitted_at = $now;
            $attempt->scored_at = $now;
            $attempt->score = $score;
            $attempt->save();

            // create log
            ExamLog::create([
                'exam_attempt_id' => $attempt->id,
                'user_id' => $user->id,
                'action' => 'submit',
                'metadata' => ['total_questions' => $total, 'correct' => $correct, 'score' => $score],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            // decide what to show based on exam settings
            $canReview = $exam->review_allowed;
            $showAnswers = false;
            if ($exam->show_answers_after_close && $exam->close_at && now()->gt($exam->close_at)) {
                $showAnswers = true;
            }

            // redirect to attempt show / result page
            return redirect()->route('attempts.show', $attempt->id)
                // ->with('success', 'Your exam was submitted. Score: ' . $score)
                ->with('success', __('messages.exam_submitted_successfully', ['score' => $score]))
                ->with('review', $canReview)
                ->with('show_answers', $showAnswers);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Exam submission failed: ' . $e->getMessage());
            // return redirect()->route('exams.show', $exam)->withErrors(['submit' => 'Unable to submit exam — please try again.']);
            return redirect()->route('exams.show', $exam)->withErrors(['submit' => __('errors.exam_submit_failed')]);
        }
    }
}
