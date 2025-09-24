<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarkRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
