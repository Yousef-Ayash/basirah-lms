<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportsService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Level;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MarksExport;

class ReportController extends Controller
{
    public function index()
    {
        // 2. Fetch all the data needed for the dropdowns
        $levels = Level::orderBy('order')->get(['id', 'name']);
        $subjects = Subject::orderBy('title')->get(['id', 'title']);
        $exams = Exam::orderBy('title')->get(['id', 'title']);
        $users = User::role('student')->orderBy('name')->get(['id', 'name']);

        // 3. Pass all four arrays to the Inertia view
        return Inertia::render('Admin/Reports/Index', [
            'levels' => $levels,
            'subjects' => $subjects,
            'exams' => $exams,
            'users' => $users,
        ]);
    }

    public function perStudentExam(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $data = ReportsService::perStudentExam($request->user_id, $request->exam_id);

        $user = User::find($request->user_id);
        $exam = Exam::find($request->exam_id);

        return Inertia::render('Admin/Reports/StudentExam', [
            'data' => $data,
            'user' => $user,
            'exam' => $exam,
        ]);
    }

    public function getExamsForStudent(User $user)
    {
        // Find all unique exam IDs from the student's attempts
        $examIds = $user->attempts()->distinct()->pluck('exam_id');

        // Fetch the corresponding exams
        $exams = Exam::whereIn('id', $examIds)->orderBy('title')->get(['id', 'title']);

        return response()->json($exams);
    }

    public function perSubjectAggregate(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'pass_threshold' => 'nullable|numeric|min:0|max:100',
        ]);

        $passThreshold = $request->input('pass_threshold', 50);
        $data = ReportsService::perSubjectAggregate($request->subject_id, (float) $passThreshold);
        $subject = Subject::find($request->subject_id);

        return Inertia::render('Admin/Reports/Subject', [
            'subject' => $subject,
            'data' => $data,
            'pass_threshold' => $passThreshold,
        ]);
    }

    public function perLevelSummary(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'pass_threshold' => 'nullable|numeric|min:0|max:100',
        ]);

        $passThreshold = $request->input('pass_threshold', 50);
        $result = ReportsService::perLevelSummary($request->level_id, (float) $passThreshold);
        $level = Level::find($request->level_id);

        return Inertia::render('Admin/Reports/Level', [
            'level' => $level,
            'result' => $result,
            'pass_threshold' => $passThreshold,
        ]);
    }

    /**
     * Generic export endpoint - supports exporting marks or reports as CSV/XLSX.
     * 'type' param can be 'marks' or other types (we currently use MarksExport).
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'format' => 'nullable|in:xlsx,csv',
        ]);

        $format = $request->input('format', 'xlsx');
        $type = $request->input('type');

        if ($type === 'marks') {
            $filters = $request->only(['user_id', 'exam_id', 'date_from', 'date_to']);
            $fileName = 'marks-report-' . now()->format('Ymd_His') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');
            return Excel::download(new MarksExport($filters), $fileName);
        }

        // abort(400, 'Unknown export type');
        abort(400, __('errors.unknown_export_type'));
    }
}
