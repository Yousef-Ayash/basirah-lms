<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'open_at' => 'nullable|date',
            'close_at' => 'nullable|date|after:open_at',
            'max_attempts' => 'required|integer|min:1',
            'questions_to_display' => 'nullable|integer|min:0',
            'full_mark' => 'nullable|integer|min:0',
            'review_allowed' => 'boolean',
            'show_answers_after_close' => 'boolean',
        ];
    }
}
