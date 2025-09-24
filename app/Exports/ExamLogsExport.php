<?php
namespace App\Exports;

use App\Models\ExamLog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class ExamLogsExport implements FromCollection
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ExamLog::query()->with(['user', 'attempt', 'attempt.exam']);

        if (!empty($this->filters['user_id']))
            $query->where('user_id', $this->filters['user_id']);
        if (!empty($this->filters['exam_id']))
            $query->where('exam_id', $this->filters['exam_id']);
        if (!empty($this->filters['action']))
            $query->where('action', $this->filters['action']);
        if (!empty($this->filters['ip']))
            $query->where('ip', $this->filters['ip']);
        if (!empty($this->filters['date_from']))
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        if (!empty($this->filters['date_to']))
            $query->whereDate('created_at', '<=', $this->filters['date_to']);

        $rows = $query->orderBy('created_at', 'desc')->get();

        $collection = $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'exam_id' => $r->exam_id,
                'exam_title' => $r->attempt?->exam?->title ?? null,
                'attempt_id' => $r->exam_attempt_id,
                'user_id' => $r->user_id,
                'user_name' => $r->user?->name ?? null,
                'action' => $r->action,
                'ip' => $r->ip,
                'user_agent' => $r->user_agent,
                'metadata' => json_encode($r->metadata),
                'created_at' => $r->created_at,
            ];
        });

        // Prepend header row (Excel will use keys as headers if FromCollection returns associative array)
        return new Collection($collection);
    }
}
