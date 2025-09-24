<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * This method fetches the data, reusing the same filtering logic
     * as your Admin/Students/Index.vue page.
     */
    public function collection()
    {
        $query = User::role('student')->with('level');

        if (!empty($this->filters['q'])) {
            $query->where(function ($s) {
                $s->where('name', 'like', "%{$this->filters['q']}%")
                    ->orWhere('email', 'like', "%{$this->filters['q']}%");
            });
        }
        if (!empty($this->filters['level_id'])) {
            $query->where('level_id', $this->filters['level_id']);
        }
        if (isset($this->filters['is_approved']) && $this->filters['is_approved'] !== '') {
            $query->where('is_approved', (bool) $this->filters['is_approved']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * This method defines the header row for the spreadsheet.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Level',
            'Approval Status',
            'Date Registered',
        ];
    }

    /**
     * This method transforms each student object into an array for the export.
     *
     * @param User $student
     */
    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->email,
            $student->level->name ?? 'N/A',
            $student->is_approved ? 'Approved' : 'Pending',
            $student->created_at->toDateTimeString(),
        ];
    }
}