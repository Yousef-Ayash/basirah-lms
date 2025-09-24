<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\ExamQuestionsExport;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get all filters from the request
        $filters = $request->only(['q', 'subject_id']);

        $query = Exam::with('subject', 'creator');

        if ($filters['q'] ?? false) {
            $query->where('title', 'like', "%{$filters['q']}%");
        }

        // 2. Add the new filtering logic for subject_id
        if ($filters['subject_id'] ?? false) {
            $query->where('subject_id', $filters['subject_id']);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // 3. Fetch all subjects to populate the filter dropdown
        $subjects = Subject::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Admin/Exams/Index', [
            'exams' => $exams,
            'subjects' => $subjects, // <-- Pass subjects to the view
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $subjects = Subject::orderBy('title')->get();
        return Inertia::render('Admin/Exams/Create', [
            'subjects' => $subjects,
        ]);
    }

    public function store(StoreExamRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        Exam::create($data);

        // return redirect()->route('admin.exams.index')->with('success', 'Exam created.');
        return redirect()->route('admin.exams.index')->with('success', __('messages.exam_created'));
    }

    public function show(Exam $exam)
    {
        // Eager-load both the 'questions' and the 'subject' relationships
        $exam->load('questions', 'subject');

        return Inertia::render('Admin/Exams/Show', [
            'exam' => $exam
        ]);
    }

    public function exportQuestions(Exam $exam)
    {
        $fileName = 'questions-' . Str::slug($exam->title) . '.xlsx';
        return Excel::download(new ExamQuestionsExport($exam), $fileName);
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::orderBy('title')->get();
        return Inertia::render('Admin/Exams/Edit', [
            'exam' => $exam,
            'subjects' => $subjects
        ]);
    }

    public function update(StoreExamRequest $request, Exam $exam)
    {
        $exam->update($request->validated());
        // return redirect()->route('admin.exams.index')->with('success', 'Exam updated.');
        return redirect()->route('admin.exams.index')->with('success', __('messages.exam_updated'));
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        // return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
        return redirect()->route('admin.exams.index')->with('success', __('messages.exam_deleted'));
    }

    // Admin: attempt list
    public function attempts(Exam $exam)
    {
        $attempts = $exam->attempts()->with('user')->orderBy('created_at', 'desc')->paginate(15);
        return Inertia::render('Admin/Exams/AttemptsIndex', [
            'exam' => $exam,
            'attempts' => $attempts,
        ]);
    }

    // Admin: show single attempt with logs
    public function attemptShow(Exam $exam, $attemptId)
    {
        // Fetch the specific attempt and eager-load all necessary relationships at once
        // to prevent N+1 query issues and ensure the view has all its data.
        $attempt = $exam->attempts()
            ->with([
                'user',
                'answers',
                'logs' => fn($query) => $query->orderBy('created_at', 'desc'),
                'exam.questions' => fn($query) => $query->orderBy('order'),
            ])
            ->where('id', $attemptId)
            ->firstOrFail();

        return Inertia::render('Admin/Exams/AttemptShow', [
            'attempt' => $attempt,
            'logs' => $attempt->logs,
            'questions' => $attempt->exam->questions, // Pass the questions to the view
        ]);
    }
}
