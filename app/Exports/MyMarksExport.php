<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MyMarksExport implements FromCollection, WithHeadings, WithMapping
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Fetches all submitted exam attempts for the specific user.
     */
    public function collection()
    {
        return $this->user->attempts()
            ->with('exam.subject') // Eager-load relationships for performance
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Defines the header row for the spreadsheet.
     */
    public function headings(): array
    {
        return [
            'Exam Title',
            'Subject',
            'Your Score',
            'Date Submitted',
        ];
    }

    /**
     * Maps the data for each row in the spreadsheet.
     *
     * @param \App\Models\StudentExamAttempt $attempt
     */
    public function map($attempt): array
    {
        return [
            $attempt->exam->title ?? 'N/A',
            $attempt->exam->subject->title ?? 'N/A',
            $attempt->score !== null ? "{$attempt->score}%" : 'Pending',
            $attempt->submitted_at->toDateTimeString(),
        ];
    }
}