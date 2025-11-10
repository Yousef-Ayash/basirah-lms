<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\ImportMarksRequest;
use App\Models\MarksReport;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MarksExport;
use App\Models\StudentExamAttempt;

class MarkController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'exam_id', 'date_from', 'date_to']);
        $query = MarksReport::query()->with(['user', 'exam.subject', 'creator', 'updater']);

        if ($filters['user_id'] ?? false)
            $query->where('user_id', $filters['user_id']);
        if ($filters['exam_id'] ?? false)
            $query->where('exam_id', $filters['exam_id']);
        if ($filters['date_from'] ?? false)
            $query->whereDate('created_at', '>=', $filters['date_from']);
        if ($filters['date_to'] ?? false)
            $query->whereDate('created_at', '<=', $filters['date_to']);

        $marks = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $exams = Exam::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Marks/Index', [
            'marks' => $marks,
            'exams' => $exams,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $exams = Exam::orderBy('title')->get();
        $users = User::orderBy('name')->get();
        return Inertia::render('Admin/Marks/Create', compact('exams', 'users'));
    }

    // public function store(StoreMarkRequest $request)
    // {
    //     $data = $request->validated();
    //     $data['created_by'] = auth()->id();
    //     $data['updated_by'] = auth()->id();

    //     MarksReport::create($data);

    //     // If attempt_id provided, sync attempt fields as well
    //     if (!empty($data['attempt_id'])) {
    //         $attempt = StudentExamAttempt::find($data['attempt_id']);
    //         if ($attempt) {
    //             $totalPossible = $attempt->exam->questions()->sum(fn($q) => $q->mark ?? 1);
    //             $attempt->mark = $data['marks'];
    //             $attempt->score = $totalPossible > 0 ? round(($data['marks'] / $totalPossible) * 100, 2) : null;
    //             $attempt->scored_at = now();
    //             $attempt->submitted_at = $attempt->submitted_at ?? now();
    //             $attempt->passed = ($attempt->score !== null) ? ($attempt->score >= ($attempt->exam->pass_threshold ?? 50)) : null;
    //             $attempt->passed_at = $attempt->passed ? now() : null;
    //             $attempt->save();
    //         }
    //     } else {
    //         // Try to find an attempt for this user + exam (optional sync)
    //         $attempt = StudentExamAttempt::where('user_id', $data['user_id'])
    //             ->where('exam_id', $data['exam_id'])->latest('created_at')->first();
    //         if ($attempt) {
    //             $totalPossible = $attempt->exam->questions()->sum(fn($q) => $q->mark ?? 1);
    //             $attempt->mark = $data['marks'];
    //             $attempt->score = $totalPossible > 0 ? round(($data['marks'] / $totalPossible) * 100, 2) : null;
    //             $attempt->scored_at = now();
    //             $attempt->submitted_at = $attempt->submitted_at ?? now();
    //             $attempt->passed = ($attempt->score !== null) ? ($attempt->score >= ($attempt->exam->pass_threshold ?? 50)) : null;
    //             $attempt->passed_at = $attempt->passed ? now() : null;
    //             $attempt->save();
    //         }
    //     }

    //     // return redirect()->route('admin.marks.index')->with('success', 'Mark added.');
    //     return redirect()->route('admin.marks.index')->with('success', __('messages.mark_added'));
    // }

    public function store(StoreMarkRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        DB::beginTransaction();
        try {
            // Load exam
            $exam = Exam::find($data['exam_id']);
            // Determine total possible points for scoring
            if ($exam) {
                $totalPossible = $exam->full_mark
                    ?? $exam->max_score
                    ?? $exam->total_score
                    ?? $exam->total_marks
                    ?? 0;
            } else {
                $totalPossible = 0;
            }

            // Fallback: if exam does not provide a total, try sum of question marks
            if (!$totalPossible) {
                try {
                    $totalPossible = \App\Models\Question::where('exam_id', $data['exam_id'])
                        ->sum(fn($q) => $q->mark ?? 1);
                } catch (\Throwable $e) {
                    $totalPossible = 0;
                }
            }

            // Compute score as percent (0..100) if we have a total
            $marksValue = is_numeric($data['marks']) ? (float) $data['marks'] : null;
            $score = ($totalPossible > 0 && $marksValue !== null)
                ? round(($marksValue / $totalPossible) * 100, 2)
                : null;

            // determine pass threshold percent
            $passThreshold = $exam->pass_threshold ?? 50;

            // If attempt_id provided -> update that attempt (existing behavior)
            if (!empty($data['attempt_id'])) {
                $attempt = StudentExamAttempt::find($data['attempt_id']);
                if ($attempt) {
                    // sync attempt fields
                    $attempt->mark = $marksValue;
                    $attempt->score = $score;
                    $attempt->scored_at = now();
                    $attempt->submitted_at = $attempt->submitted_at ?? now();
                    $attempt->passed = ($score !== null) ? ($score >= $passThreshold) : null;
                    $attempt->passed_at = $attempt->passed ? now() : null;
                    $attempt->save();
                }
            } else {
                // No attempt_id -> create a new attempt (same behavior as importCommit)
                // compute correct attempt_number
                $existingCount = StudentExamAttempt::where('user_id', $data['user_id'])
                    ->where('exam_id', $data['exam_id'])
                    ->count();

                $attempt = StudentExamAttempt::create([
                    'exam_id' => $data['exam_id'],
                    'user_id' => $data['user_id'],
                    'started_at' => now(),
                    'submitted_at' => now(),
                    'scored_at' => now(),
                    'mark' => $marksValue,
                    'score' => $score,
                    'attempt_number' => $existingCount + 1,
                    'metadata' => $data['metadata'] ?? null,
                ]);

                // set passed/failed fields on created attempt
                $attempt->passed = ($score !== null) ? ($score >= $passThreshold) : null;
                $attempt->passed_at = $attempt->passed ? now() : null;
                $attempt->save();
            }

            // create MarksReport and attach attempt_id
            $marksReportData = [
                'user_id' => $data['user_id'],
                'exam_id' => $data['exam_id'],
                'marks' => $marksValue,
                // include score if your marks_reports table supports it
                'score' => $score,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by'],
                'attempt_id' => $attempt->id ?? null,
            ];

            MarksReport::create($marksReportData);

            DB::commit();
            return redirect()->route('admin.marks.index')->with('success', __('messages.mark_added'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Mark store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['mark' => __('messages.mark_add_failed')]);
        }
    }


    public function edit(MarksReport $mark)
    {
        $mark->load('user');
        $exams = Exam::orderBy('title')->get();
        $users = User::orderBy('name')->get();
        return Inertia::render('Admin/Marks/Edit', compact('mark', 'exams', 'users'));
    }

    public function update(StoreMarkRequest $request, MarksReport $mark)
    {
        $exam = Exam::firstWhere('id', $mark->exam_id);
        $score = ((float) $request->get('marks') / $exam->full_mark) * 100;

        $mark->update(array_merge($request->validated(), ['score' => (double) round($score, 2), 'updated_by' => auth()->id()]));

        // return redirect()->route('admin.marks.index')->with('success', 'Mark updated.');
        return redirect()->route('admin.marks.index')->with('success', __('messages.mark_updated'));
    }

    public function destroy(MarksReport $mark)
    {
        $mark->delete();
        // return redirect()->route('admin.marks.index')->with('success', 'Mark deleted.');
        return redirect()->route('admin.marks.index')->with('success', __('messages.mark_deleted'));
    }

    public function importForm()
    {
        return Inertia::render('Admin/Marks/Import');
    }

    /**
     * Import preview: parse and validate rows, store preview in cache (single-use) and show preview page.
     * Expected columns: user_email (or user_id), exam_id (or exam_title), marks, notes (optional)
     */
    public function importPreview(ImportMarksRequest $request)
    {
        $arr = Excel::toArray(null, $request->file('file'));
        $rows = $arr[0] ?? [];

        // optional header detection
        if (isset($rows[0]) && is_array($rows[0])) {
            $first = array_map('strtolower', array_map('trim', $rows[0]));
            $hasHeader = false;
            foreach ($first as $c) {
                if (str_contains($c, 'user') || str_contains($c, 'exam') || str_contains($c, 'marks')) {
                    $hasHeader = true;
                    break;
                }
            }
            if ($hasHeader)
                array_shift($rows);
        }

        $preview = [];
        $rowNum = 1;
        foreach ($rows as $row) {
            $rowNum++;
            $userIdentifier = $row[0] ?? null; // email or id
            $examIdentifier = $row[1] ?? null; // id or title
            $marks = isset($row[2]) ? $row[2] : null;
            $notes = isset($row[3]) ? (string) $row[3] : null;

            $errors = [];

            // resolve user
            $user = null;
            if (is_numeric($userIdentifier)) {
                $user = User::find((int) $userIdentifier);
            } elseif (is_string($userIdentifier)) {
                // } elseif (is_string($userIdentifier) && filter_var($userIdentifier, FILTER_VALIDATE_EMAIL)) {
                // $user = User::where('email', trim($userIdentifier))->first();
                $user = User::where('phone', trim($userIdentifier))->first();
            }
            if (!$user)
                $errors[] = 'User not found (provide user_id or phone).';
            // $errors[] = 'User not found (provide user_id or email).';

            // resolve exam
            $exam = null;
            if (is_numeric($examIdentifier)) {
                $exam = Exam::find((int) $examIdentifier);
            } else {
                // match by title
                if ($examIdentifier) {
                    $exam = Exam::where('title', trim($examIdentifier))->first();
                }
            }
            if (!$exam)
                $errors[] = 'Exam not found (provide exam_id or exact title).';

            // marks numeric check
            if ($marks === null || $marks === '') {
                $errors[] = 'Marks required.';
            } elseif (!is_numeric($marks)) {
                $errors[] = 'Marks must be a number.';
            } else {
                $marks = (float) $marks;
                if ($marks < 0 || $marks > 100)
                    $errors[] = 'Marks must be between 0 and 100.';
            }

            $preview[] = [
                'row' => $rowNum,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_phone' => $user?->phone,
                'exam_id' => $exam?->id,
                'exam_title' => $exam?->title,
                'marks' => $marks,
                'notes' => $notes,
                'errors' => $errors,
            ];
        }

        $previewId = (string) Str::uuid();
        Cache::put("marks_import_{$previewId}", [
            'rows' => $preview,
        ], now()->addHour());

        return Inertia::render('Admin/Marks/ImportPreview', [
            'preview' => $preview,
            'preview_id' => $previewId,
        ]);
    }

    public function importCommit(Request $request)
    {
        $request->validate(['preview_id' => 'required|string']);
        $cacheKey = "marks_import_{$request->preview_id}";
        $cached = Cache::pull($cacheKey);
        if (!$cached || !isset($cached['rows'])) {
            // return redirect()->back()->withErrors(['import' => 'Preview expired. Re-upload file.']);
            return redirect()->back()->withErrors(['import' => __('messages.import_preview_expired')]);
        }

        $rows = $cached['rows'];
        // check for errors
        foreach ($rows as $r) {
            if (!empty($r['errors'])) {
                // return redirect()->back()->withErrors(['import' => 'Preview contains errors. Fix them before committing.']);
                return redirect()->back()->withErrors(['import' => __('messages.import_has_errors')]);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $r) {
                $exam = Exam::where('id', $r['exam_id'])->first();
                // dd($exam);
                $totalPossible = $exam->full_mark;
                $score = $totalPossible ? round(($r['marks'] / $totalPossible) * 100, 2) : null;

                // Create Attempt for each mark
                $attempt = StudentExamAttempt::create([
                    'exam_id' => $r['exam_id'],
                    'user_id' => $r['user_id'],
                    'started_at' => now(),
                    'submitted_at' => now(),
                    'scored_at' => now(),
                    'mark' => $r['marks'],
                    'score' => $score,
                    'metadata' => null,
                ]);

                // create MarksReport and attach attempt_id (so the attempt->marksReport relation works)
                MarksReport::create([
                    'user_id' => $r['user_id'],
                    'exam_id' => $r['exam_id'],
                    'marks' => $r['marks'],
                    'score' => $score,
                    'notes' => $r['notes'] ?? null,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'attempt_id' => $attempt->id, // <--- important
                ]);
            }
            DB::commit();
            return redirect()->route('admin.marks.index')->with('success', __('messages.marks_imported'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Marks import commit failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['import' => __('messages.import_commit_failed')]);
        }
    }

    public function export(Request $request)
    {
        $filters = $request->only(['user_id', 'exam_id', 'date_from', 'date_to', 'format']);
        $format = $filters['format'] ?? 'xlsx';
        $fileName = 'marks-' . now()->format('Ymd_His') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');

        return Excel::download(new MarksExport($filters), $fileName);
    }
}
