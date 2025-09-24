<?php

namespace App\Exports;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamQuestionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Exam $exam;

    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * The collection of questions to be exported.
     */
    public function collection()
    {
        return $this->exam->questions()->orderBy('order')->get();
    }

    /**
     * Defines the header row for the spreadsheet.
     */
    public function headings(): array
    {
        return [
            'Question Text',
            'Option 1',
            'Option 2',
            'Option 3',
            'Option 4',
            'Option 5',
            'Correct Answer (Text)',
            'Correct Answer (Index)',
        ];
    }

    /**
     * Maps the data for each row in the spreadsheet.
     *
     * @param \App\Models\ExamQuestion $question
     */
    public function map($question): array
    {
        $options = $question->options ?? [];
        $correctAnswerIndex = $question->correct_answer - 1; // Convert 1-based to 0-based

        return [
            $question->question_text,
            $options[0] ?? '',
            $options[1] ?? '',
            $options[2] ?? '',
            $options[3] ?? '',
            $options[4] ?? '',
            $options[$correctAnswerIndex] ?? 'N/A',
            $question->correct_answer,
        ];
    }
}