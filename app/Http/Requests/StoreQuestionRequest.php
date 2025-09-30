<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|integer|min:1',
            'mark' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $options = $this->input('options', []);
            $correct = (int) $this->input('correct_answer', 0);
            if ($correct < 1 || $correct > count($options)) {
                $v->errors()->add('correct_answer', 'Correct answer index is out of range for options.');
            }
        });
    }
}
