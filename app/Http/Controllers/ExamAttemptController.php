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
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MarksReport;
use App\Exports\MyAttemptsExport;

class ExamAttemptController extends Controller
{
    // POST /exams/{exam}/start
    public function start(Request $request, Exam $exam)
    {
        $user = $request->user();
        $now = now();

        // check open/close
        if ($exam->open_at && $now->lt($exam->open_at)) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'الاختبار لم يفتح بعد.']);
        }
        if ($exam->close_at && $now->gt($exam->close_at)) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'الاختبار مغلق بالفعل.']);
        }

        // attempts check
        $attemptCount = StudentExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'تم الوصول إلى الحد الأقصى من المحاولات.']);
        }

        if ($exam->questions()->count() === 0) {
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'هذا الاختبار لا يحتوي على أسئلة ولا يمكن البدء به.']);
        }

        $last_attempt = StudentExamAttempt::where('exam_id', $exam->id)->where('user_id', $user->id)->latest('submitted_at')->value('submitted_at');

        if ($last_attempt) {
            $submittedDate = Carbon::parse($last_attempt)->startOfDay();
            $nowDay = now()->startOfDay();

            if (!$submittedDate->lte($nowDay->subDays($exam->distance_between_attempts))) {
                return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'لم يحن موعد محاولة التقديم التالية.']);
            }
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
        $user = $request->user();

        // 1. collect filters from request (keeps it tidy and consistent)
        $filters = $request->only(['q', 'exam_id', 'status', 'from', 'to', 'per_page']);

        // default per page
        $perPage = (int) ($filters['per_page'] ?? 15);
        if ($perPage <= 0)
            $perPage = 15;

        // 2. base query scoped to current user with eager loads
        $query = StudentExamAttempt::with([
            'exam',
            'marksReport' => function ($q) {
                $q->with('creator');
            },
        ])->where('user_id', $user->id);

        // 3. apply filters (search by exam title)
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->whereHas('exam', function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%");
            });
        }

        // 4. filter by a specific exam id (dropdown)
        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        // 5. date range filter (filter by created_at; change to submitted_at if preferred)
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        // 6. status filter - kept intentionally simple and performant for common cases
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            switch ($filters['status']) {
                case 'in_progress':
                    $query->whereNull('submitted_at');
                    break;

                case 'submitted':
                    $query->whereNotNull('submitted_at');
                    break;

                case 'passed':
                    // prefer marks_report when present (official), otherwise use attempt.passed/score
                    $query->where(function ($qb) {
                        $qb->whereHas('marksReport', function ($m) {
                            $m->where('official', true)
                                // compare marks against exam.pass_threshold using subquery
                                ->whereColumn('marks_reports.marks', '>=', DB::raw('(SELECT COALESCE(pass_threshold, 50) FROM exams WHERE exams.id = student_exam_attempts.exam_id)'));
                        })->orWhere('passed', true)->orWhere(function ($sub) {
                            $sub->whereNotNull('score')->where('score', '>=', 50);
                        });
                    });
                    break;

                case 'failed':
                    $query->where(function ($qb) {
                        $qb->whereHas('marksReport', function ($m) {
                            $m->where('official', true)
                                ->whereColumn('marks_reports.marks', '<', DB::raw('(SELECT COALESCE(pass_threshold, 50) FROM exams WHERE exams.id = student_exam_attempts.exam_id)'));
                        })->orWhere('passed', false)->orWhere(function ($sub) {
                            $sub->whereNotNull('score')->where('score', '<', 50);
                        });
                    });
                    break;
            }
        }

        // 7. ordering + pagination, preserve query string
        $attempts = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 8. provide lightweight exam list for the exam filter dropdown
        $examOptions = Exam::orderBy('title')->get(['id', 'title']);

        // 9. return to Inertia with attempts, filters and exam options
        return Inertia::render('Attempts/Index', [
            'attempts' => $attempts,
            'filters' => $filters,
            'examOptions' => $examOptions,
        ]);
    }

    /**
     * Download the student's attempts listing (the same page they see) as XLSX.
     * Query params:
     *  - page (optional) : int (paginates same as UI)
     *  - per_page (optional): int (items per page)
     *  - user_id (optional, admin-only): export for specific user
     */
    public function exportAttemptsExcel(Request $request)
    {
        $user = $request->user();

        // Optionally allow admins to pass ?user_id=123 to export other user's list
        $targetUserId = $request->query('user_id');
        if ($targetUserId && $user->hasRole('admin')) {
            $scopedUserId = (int) $targetUserId;
        } else {
            $scopedUserId = $user->id;
        }

        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);

        // Query attempts exactly as in your index (eager load marksReport)
        $query = StudentExamAttempt::with(['exam', 'marksReport.creator'])
            ->where('user_id', $scopedUserId)
            ->orderBy('created_at', 'desc');

        // apply pagination to match the UI page
        $attemptsPage = $query->paginate($perPage, ['*'], 'page', $page);

        $fileName = 'my-attempts-' . ($scopedUserId === $user->id ? 'me' : 'user-' . $scopedUserId) . '-' . now()->format('Ymd_His') . '.xlsx';

        // Use a small export class that accepts a Collection or array of attempts
        return Excel::download(new MyAttemptsExport($attemptsPage->items()), $fileName);
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
            return redirect()->route('exams.show', $exam)->withErrors(['attempt' => 'تم تسليم المحاولة بالفعل.']);
        }

        $now = now();

        // re-check attempt count and open/close windows
        $attemptCount = StudentExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attempt->attempt_number > $exam->max_attempts) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Attempt exceeded allowed attempts.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'تم الوصول إلى الحد الأقصى من المحاولات.']);
        }

        if ($exam->open_at && $now->lt($exam->open_at)) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Exam not open.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'الاختبار لم يفتح بعد.']);
        }

        if ($exam->close_at && $now->gt($exam->close_at)) {
            // return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'Exam closed.']);
            return redirect()->route('exams.show', $exam)->withErrors(['exam' => 'الاختبار مغلق بالفعل.']);
        }

        // time limit check if exists

        if ($exam->time_limit_minutes) {
            $expires = $attempt->started_at->copy()->addMinutes($exam->time_limit_minutes);
            if ($now->gt($expires)) {
                // return redirect()->route('exams.show', $exam)->withErrors(['time' => 'Time limit exceeded.']);
                return redirect()->route('exams.show', $exam)->withErrors(['time' => 'تم تجاوز الحد الزمني.']);
            }
        }

        // MAKE SURE THIS IS NULLABLE OR ELSE THE SUBMIT WILL BREAK;
        $data = $request->validate([
            'answers' => 'nullable|array',
            'answers.*' => 'nullable|integer'
        ]);

        $answersPayload = $data['answers'] ?? [];

        DB::beginTransaction();
        try {

            // Get the specific question IDs that were part of this attempt from metadata
            $questionIdsForThisAttempt = $attempt->metadata['question_ids'] ?? null;

            $questions = $questionIdsForThisAttempt ?
                // If specific questions were stored, fetch only those
                ExamQuestion::whereIn('id', $questionIdsForThisAttempt)
                    ->get()
                    ->keyBy('id')
                // Fallback for older attempts: get all questions for the exam
                : $exam->questions()->get()->keyBy('id');

            $total = $questions->count();
            $correct = 0;
            $mark = 0.0;

            foreach ($answersPayload as $qid => $sel) {
                $qid = (int) $qid;
                if (!isset($questions[$qid]))
                    continue;
                $q = $questions[$qid];
                $isCorrect = ((int) $sel === (int) $q->correct_answer);
                if ($isCorrect) {
                    $correct++;
                    $mark += $q->mark;
                }
                StudentExamAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $qid],
                    [
                        'selected_option' => $sel === null ? null : (int) $sel,
                        'is_correct' => $isCorrect,
                        'score_awarded' => $isCorrect ? $q->mark : 0,
                        'graded_at' => now()
                    ]
                );
            }

            $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0.0;

            $attempt->submitted_at = $now;
            $attempt->scored_at = $now;
            $attempt->score = $score;
            $attempt->mark = $mark;
            $passThreshold = $exam->pass_threshold ?? $exam->full_mark * 0.5; // your model uses pass_threshold
            $attempt->passed = ($score >= $passThreshold);
            $attempt->passed_at = $attempt->passed ? now() : null;
            $attempt->save();

            $mark_report = MarksReport::updateOrCreate(
                ['attempt_id' => $attempt->id],
                [
                    'user_id' => $attempt->user_id,
                    'exam_id' => $exam->id,
                    'marks' => $mark,
                    'score' => $score,
                    'notes' => null,
                    'created_by' => null,
                    'updated_by' => null,
                    'official' => true,
                    'published_at' => null,
                ]
            );

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
                ->with('success', 'تم تسليم اختبارك. الدرجة: ' . ['score' => $score])
                ->with('review', $canReview)
                ->with('show_answers', $showAnswers);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Exam submission failed: ' . $e->getMessage(), [
                'exception' => $e,
                'attempt_id' => $attempt->id ?? null,
                'user_id' => $user->id ?? null,
                'exam_id' => $exam->id ?? null,
                'answers_payload' => $answersPayload ?? null
            ]);
            // return redirect()->route('exams.show', $exam)->withErrors(['submit' => 'Unable to submit exam — please try again.']);
            return redirect()->route('exams.show', $exam)->withErrors(['submit' => 'فشل تسليم اختبارك.']);
        }
    }
}
