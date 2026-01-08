<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            // Allow images up to 10MB
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }
}
