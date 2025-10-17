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

        if (!isset($data['pass_threshold'])) {
            $data['pass_threshold'] = 50;
        }

        Exam::create($data);

        // return redirect()->route('admin.exams.index')->with('success', 'Exam created.');
        return redirect()->route('admin.exams.index')->with('success', 'تم إنشاء الاختبار.');
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
        return redirect()->route('admin.exams.index')->with('success', 'تم تحديث الاختبار.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        // return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
        return redirect()->route('admin.exams.index')->with('success', 'تم حذف الاختبار.');
    }

    // Admin: attempt list
    public function attempts(Exam $exam)
    {
        // $attempts = $exam->attempts()->with('user')->orderBy('created_at', 'desc')->paginate(15);
        $attempts = $exam->attempts()
            ->with([
                'user',
                'marksReport' => function ($q) {
                    $q->with('creator');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // return Inertia::render('Admin/Exams/AttemptsIndex', [
        //     'exam' => $exam,
        //     'attempts' => $attempts,
        // ]);

        return Inertia::render('Admin/Attempts/Index', [
            'exam' => $exam,
            'attempts' => $attempts // Laravel paginator with ->with('user','marksReport.creator')
        ]);
    }

    // Admin: show single attempt with logs
    public function attemptShow(Exam $exam, $attemptId)
    {
        // Fetch the attempt and eager-load all relationships needed for the view.
        // This prevents N+1 query problems.
        $attempt = $exam->attempts()
            ->with([
                'user:id,name,email', // Only get needed columns from user
                'answers.question',   // Get answers and their corresponding questions
                'logs' => fn($query) => $query->orderBy('created_at', 'desc'), // Get logs in order
                'marksReport.creator:id,name', // Get the linked marks report and who created it
            ])
            ->where('id', $attemptId)
            ->firstOrFail();

        // Pass the fully loaded attempt object to the Vue component.
        return Inertia::render('Admin/Attempts/Show', [
            'attempt' => $attempt,
            'exam' => $exam // Pass the exam too for context
        ]);
    }
}
