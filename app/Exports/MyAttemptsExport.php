<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MyAttemptsExport implements FromArray, WithHeadings
{
    protected $attempts;

    public function __construct(array $attempts)
    {
        $this->attempts = $attempts;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->attempts as $a) {
            $examTitle = data_get($a, 'exam.title', '');
            $started = data_get($a, 'started_at') ? date('Y-m-d H:i:s', strtotime($a['started_at'])) : '';
            $submitted = data_get($a, 'submitted_at') ? date('Y-m-d H:i:s', strtotime($a['submitted_at'])) : '';
            $score = data_get($a, 'score', '');
            $finalMark = data_get($a, 'marks_report.marks') ?? data_get($a, 'mark', '');
            $status = data_get($a, 'marks_report') ? (($a['marks_report']['official'] ?? false) ? ($a['marks_report']['marks'] >= ($a['exam']['pass_threshold'] ?? 50) ? 'Passed' : 'Failed') : 'Unverified') : (($a['passed'] ?? false) ? 'Passed' : 'Failed');

            $rows[] = [
                'exam' => $examTitle,
                'started' => $started,
                'submitted' => $submitted,
                'score' => $score,
                'final_mark' => $finalMark,
                'status' => $status,
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Exam', 'Started', 'Submitted', 'Score %', 'Final mark', 'Status'];
    }
}
