<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\MaterialController;
use App\Services\YouTubeService;

class StoreMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    protected function prepareForValidation()
    {
        $routeSubject = $this->route('subject');
        if ($routeSubject) {
            $subjectId = is_object($routeSubject) ? $routeSubject->id : $routeSubject;
            $this->merge(['subject_id' => $subjectId]);
        }
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:191'],
            'key_points' => ['nullable', 'string'],
            'type' => ['required', 'in:pdf,picture,youtube'],
            'youtube_link' => ['nullable', 'url'],
            'file' => ['nullable', 'file'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $type = $this->input('type');
            if (in_array($type, ['pdf', 'picture']) && !$this->hasFile('file')) {
                $v->errors()->add('file', 'File is required for pdf/picture.');
            }

            if ($type === 'pdf' && $this->hasFile('file')) {
                $file = $this->file('file');
                if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                    $v->errors()->add('file', 'File must be a PDF.');
                }
                if ($file->getSize() / 1024 > 30720) {
                    $v->errors()->add('file', 'PDF max size is 30MB.');
                }
            }

            if ($type === 'picture' && $this->hasFile('file')) {
                $file = $this->file('file');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $v->errors()->add('file', 'Image must be jpg, jpeg, png or webp.');
                }
                if ($file->getSize() / 1024 > 10240) {
                    $v->errors()->add('file', 'Image max size is 10MB.');
                }
            }

            if ($type === 'youtube' && $this->input('youtube_link')) {
                if (!YouTubeService::extractYoutubeId($this->input('youtube_link'))) {
                    $v->errors()->add('youtube_link', 'Invalid YouTube link.');
                }
            }
        });
    }
}