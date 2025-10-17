<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create subjects') || $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'level_id' => 'required|exists:levels,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240', // 10MB
        ];
    }
}
