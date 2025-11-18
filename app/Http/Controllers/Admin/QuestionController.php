<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\ImportQuestionsRequest;
use App\Models\Exam;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class QuestionController extends Controller
{
    // list questions
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->orderBy('order')->paginate(25);
        return Inertia::render('Admin/Questions/Index', [
            'exam' => $exam,
            'questions' => $questions,
        ]);
    }

    public function create(Exam $exam)
    {
        return Inertia::render('Admin/Questions/Create', ['exam' => $exam]);
    }

    public function store(StoreQuestionRequest $request, Exam $exam)
    {
        $data = $request->validated();
        $data['exam_id'] = $exam->id;

        // ensure options count between 3 and 5 and correct_answer within bounds handled in request
        ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_text' => $data['question_text'],
            'options' => $data['options'],
            'correct_answer' => $data['correct_answer'],
            'mark' => $data['mark'] ?? 2.5,
            'order' => $exam->questions()->max('order') + 1,
        ]);

        // return redirect()->route('admin.exams.questions.index', $exam->id)->with('success', 'Question added.');
        return redirect()->route('admin.exams.questions.index', $exam->id)->with('success', __('messages.question_added'));
    }

    // in app/Http/Controllers/Admin/QuestionController.php

    public function storeBatch(Request $request, Exam $exam)
    {
        // Validate that we received an array of questions
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correct_answer' => 'required|integer|min:1',
            'questions.*.mark' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $order = $exam->questions()->max('order') ?? 0;
            foreach ($validated['questions'] as $questionData) {
                // Additional validation to ensure correct_answer is in range
                if ($questionData['correct_answer'] > count($questionData['options'])) {
                    throw new \Exception('Correct answer index is out of range.');
                }

                $order++;
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_text' => $questionData['question_text'],
                    'options' => $questionData['options'],
                    'correct_answer' => $questionData['correct_answer'],
                    'mark' => $questionData['mark'],
                    'order' => $order,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Exam batch import failed: ' . $e->getMessage());
            // return redirect()->back()->withErrors(['import' => 'Unable to save questions. Please check your data.']);
            return redirect()->back()->withErrors(['import' => __('messages.questions_save_failed')]);
        }

        // return redirect()->route('admin.exams.show', $exam->id)->with('success', 'Questions added successfully.');
        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('messages.questions_added'));
    }

    public function edit(Exam $exam, ExamQuestion $question)
    {
        return Inertia::render('Admin/Questions/Edit', [
            'exam' => $exam,
            'question' => $question,
        ]);
    }

    public function update(StoreQuestionRequest $request, Exam $exam, ExamQuestion $question)
    {
        $question->update($request->validated());
        // return redirect()->route('admin.exams.show', $exam->id)->with('success', 'Question updated.');
        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('messages.question_updated'));
    }

    public function destroyAll(Exam $exam)
    {
        $exam->questions()->delete();
        // return redirect()->back()->with('success', 'All questions have been deleted.');
        return redirect()->back()->with('success', __('messages.questions_all_deleted'));
    }

    public function destroy(Exam $exam, ExamQuestion $question)
    {
        $question->delete();
        // return redirect()->back()->with('success', 'Question removed.');
        return redirect()->back()->with('success', __('messages.question_removed'));
    }

    /**
     * Import preview - parse rows, validate them and cache the parsed rows for commit.
     * Returns an Inertia page with preview data and preview_id.
     */
    public function importPreview(ImportQuestionsRequest $request, Exam $exam)
    {
        $file = $request->file('file');
        $arr = Excel::toArray(null, $file); // returns array of sheets; we use first sheet
        $rows = $arr[0] ?? [];

        // detect header: if first row contains 'question' or 'question_text' etc
        if (isset($rows[0]) && is_array($rows[0])) {
            $first = array_map('strtolower', array_map('trim', $rows[0]));
            $hasHeader = false;
            foreach ($first as $c) {
                if (str_contains($c, 'question') || str_contains($c, 'option1') || str_contains($c, 'correct')) {
                    $hasHeader = true;
                    break;
                }
            }
            if ($hasHeader) {
                array_shift($rows); // drop header
            }
        }

        $preview = [];
        $rowNum = 1;
        foreach ($rows as $row) {
            $rowNum++;
            // normalize row to expected columns:
            // [question_text, option1, option2, option3, option4?, option5?, correct_answer]
            $questionText = isset($row[0]) ? trim((string) $row[0]) : '';
            $options = [];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($row[$i])) {
                    $opt = trim((string) $row[$i]);
                    if ($opt !== '')
                        $options[] = $opt;
                }
            }
            $correct = null;
            if (isset($row[6])) {
                $correct = is_numeric($row[6]) ? (int) $row[6] : null;
            }
            $mark = $row[7];

            if (empty($mark)) {
                $mark = 2.5;
            }

            $errors = [];
            if ($questionText === '')
                $errors[] = 'Question text is required.';
            if (count($options) < 3)
                $errors[] = 'At least 3 non-empty options are required.';
            if ($correct === null)
                $errors[] = 'Correct answer index (1-based) is required.';
            elseif ($correct < 1 || $correct > count($options))
                $errors[] = 'Correct answer index is out of range for the provided options.';

            $preview[] = [
                'row' => $rowNum,
                'question_text' => $questionText,
                'options' => $options,
                'correct_answer' => $correct,
                'mark' => $mark,
                'errors' => $errors,
            ];
        }

        $previewId = (string) Str::uuid();
        Cache::put("exam_import_{$previewId}", [
            'exam_id' => $exam->id,
            'rows' => $preview,
        ], now()->addHour());

        return Inertia::render('Admin/Questions/ImportPreview', [
            'exam' => $exam,
            'preview_id' => $previewId,
            'preview' => $preview,
        ]);
    }

    /**
     * Commit import that was previewed. Uses cache entry referenced by preview_id.
     * Performs DB transaction and rolls back on any failure.
     */
    public function importCommit(Request $request, Exam $exam)
    {
        $request->validate(['preview_id' => 'required|string']);

        $cacheKey = "exam_import_{$request->preview_id}";
        $cached = Cache::pull($cacheKey); // pull deletes so it's single-use
        if (!$cached || !isset($cached['rows'])) {
            // return redirect()->back()->withErrors(['preview_id' => 'Import preview not found or expired. Please re-upload.']);
            return redirect()->back()->withErrors(['preview_id' => __('messages.import_preview_expired')]);
        }

        $rows = $cached['rows'];
        // check if any row had errors; if so, refuse commit
        foreach ($rows as $r) {
            if (!empty($r['errors'])) {
                // return redirect()->back()->withErrors(['import' => 'Preview contains errors. Fix them before committing.']);
                return redirect()->back()->withErrors(['import' => __('messages.import_has_errors')]);
            }
        }

        DB::beginTransaction();
        try {
            $order = $exam->questions()->max('order') ?? 0;
            foreach ($rows as $r) {
                $order++;
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_text' => $r['question_text'],
                    'options' => $r['options'],
                    'correct_answer' => $r['correct_answer'],
                    'mark' => $r['mark'],
                    'order' => $order,
                ]);
            }
            DB::commit();
            // return redirect()->route('admin.exams.questions.index', $exam->id)->with('success', 'Questions imported successfully.');
            return redirect()->route('admin.exams.questions.index', $exam->id)->with('success', __('messages.questions_added'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Exam import commit failed: ' . $e->getMessage());
            // return redirect()->back()->withErrors(['import' => 'Unable to import questions. See logs.']);
            return redirect()->back()->withErrors(['import' => __('messages.import_commit_failed')]);
        }
    }
}
