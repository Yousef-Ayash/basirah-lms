<?php

namespace App\Exports;

use App\Models\MarksReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarksExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * 1. The main query now eager-loads the extra relationships
     * we need (user's level and exam's subject).
     */
    public function collection()
    {
        $query = MarksReport::query()->with(['user.level', 'exam.subject', 'creator', 'updater']);

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['exam_id'])) {
            $query->where('exam_id', $this->filters['exam_id']);
        }
        // ... other filters if needed

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * 2. This new method defines the exact header row for the Excel file.
     */
    public function headings(): array
    {
        return [
            'Mark ID',
            'Student ID',
            'Student Name',
            'Student Level',
            'Exam ID',
            'Exam Title',
            'Subject',
            'Marks',
            'Notes',
            'Created By',
            'Updated By',
            'Date Added',
        ];
    }

    /**
     * 3. This new method transforms each $mark object into an array for the spreadsheet.
     *
     * @param MarksReport $mark
     */
    public function map($mark): array
    {
        return [
            $mark->id,
            $mark->user_id,
            $mark->user->name ?? 'N/A',
            $mark->user->level->name ?? 'N/A',
            $mark->exam_id,
            $mark->exam->title ?? 'N/A',
            $mark->exam->subject->title ?? 'N/A',
            $mark->marks,
            $mark->notes,
            $mark->creator->name ?? 'N/A',
            $mark->updater->name ?? 'N/A',
            $mark->created_at->toDateTimeString(),
        ];
    }
}