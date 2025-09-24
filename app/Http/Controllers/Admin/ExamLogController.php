<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamLog;
use App\Exports\ExamLogsExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Exam;

class ExamLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'exam_id' => 'nullable|integer|exists:exams,id',
            'action' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'ip' => 'nullable|string',
        ]);

        $query = ExamLog::query()->with(['user', 'attempt', 'attempt.exam']);

        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->exam_id)
            $query->where('exam_id', $request->exam_id);
        if ($request->action)
            $query->where('action', $request->action);
        if ($request->ip)
            $query->where('ip', $request->ip);
        if ($request->date_from)
            $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)
            $query->whereDate('created_at', '<=', $request->date_to);

        // $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // return Inertia::render('Admin/Logs/Index', [
        //     'logs' => $logs,
        //     'filters' => $request->only(['user_id', 'exam_id', 'action', 'date_from', 'date_to', 'ip']),
        // ]);

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // Add these lines to fetch data for the filters
        $users = User::role('student')->orderBy('name')->get(['id', 'name']);
        $exams = Exam::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['user_id', 'exam_id', 'action', 'date_from', 'date_to', 'ip']),
            'users' => $users,
            'exams' => $exams,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'exam_id' => 'nullable|integer|exists:exams,id',
            'action' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'ip' => 'nullable|string',
            'format' => 'nullable|in:xlsx,csv',
        ]);

        $filters = $request->only(['user_id', 'exam_id', 'action', 'date_from', 'date_to', 'ip']);
        $format = $request->input('format', 'xlsx');

        $fileName = 'exam-logs-' . now()->format('Ymd_His') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');

        return Excel::download(new ExamLogsExport($filters), $fileName);
    }
}
