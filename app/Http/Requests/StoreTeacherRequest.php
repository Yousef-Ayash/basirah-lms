<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Teacher;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    /**
     * Prepare the input for validation.
     *
     * - Treat NULL, empty string or 0 as "auto-assign".
     * - On create: auto-assign to (max(order) + 1) or 1 if none.
     * - On update: if order left blank/0 we keep the current teacher->order (so editing without changing order won't move it).
     */
    protected function prepareForValidation()
    {
        $order = $this->input('order');

        // detect route model / id for update (if any)
        $routeTeacher = $this->route('teacher');

        // normalize numeric id to teacher model if possible
        $currentTeacher = null;
        if ($routeTeacher instanceof Teacher) {
            $currentTeacher = $routeTeacher;
        } elseif (is_numeric($routeTeacher)) {
            $currentTeacher = Teacher::find($routeTeacher);
        }

        if ($order === null || $order === '' || intval($order) === 0) {
            if ($currentTeacher) {
                // update: keep the existing order if possible (avoid moving it)
                $assigned = $currentTeacher->order ?? (Teacher::max('order') ?? 0) + 1;
            } else {
                // create: assign to end (max + 1)
                $max = Teacher::max('order');
                $assigned = is_null($max) ? 1 : ($max + 1);
            }

            $this->merge(['order' => $assigned]);
        } else {
            // ensure stored as integer
            $this->merge(['order' => intval($order)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeTeacher = $this->route('teacher');
        $ignoreId = null;

        if ($routeTeacher instanceof Teacher) {
            $ignoreId = $routeTeacher->id;
        } elseif (is_numeric($routeTeacher)) {
            $ignoreId = (int) $routeTeacher;
        }

        $uniqueRule = Rule::unique('teachers', 'order');
        if ($ignoreId) {
            $uniqueRule = $uniqueRule->ignore($ignoreId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:1', $uniqueRule],
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ];
    }
}
