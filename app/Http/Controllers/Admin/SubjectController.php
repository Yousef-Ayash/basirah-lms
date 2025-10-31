<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['title', 'level_id', 'teacher_id']);

        $query = Subject::query()
            ->with('level', 'creator', 'teacher')
            ->withCount([
                'materials as youtube_materials_count' => function ($query) {
                    $query->where('type', 'youtube');
                },
                'materials as non_youtube_materials_count' => function ($query) {
                    $query->where('type', '!=', 'youtube');
                },
                'exams',
            ]);


        // Filter by subject title (using 'like' for partial match)
        if ($filters['title'] ?? false) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // Filter by subject level (using the ID from the relationship)
        if ($filters['level_id'] ?? false) {
            $query->where('level_id', $filters['level_id']);
        }

        // Filter by subject teacher (using the ID from the relationship)
        if ($filters['teacher_id'] ?? false) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        $subjects = $query->orderBy('title', 'asc')->paginate(25)->withQueryString();

        $levels = Level::orderBy('order')->get(['id', 'name']);
        $teachers = Teacher::orderBy('order')->get(['id', 'name']);

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $subjects,
            'levels' => $levels,
            'teachers' => $teachers,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $levels = Level::orderBy('order')->get();
        $teachers = Teacher::orderBy('name')->get();
        return Inertia::render('Admin/Subjects/Create', ['levels' => $levels, 'teachers' => $teachers]);
    }

    public function store(StoreSubjectRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $subject = Subject::create($data);

        if ($request->hasFile('cover')) {
            $subject->addMediaFromRequest('cover')->toMediaCollection('cover');
            $subject = $subject->fresh();
            $coverUrl = $subject->cover_url; // accessor
        }

        // return redirect()->route('admin.subjects.index')->with('success', 'Subject created.');
        return redirect()->route('admin.subjects.index')->with('success', __('messages.item_created', ['item' => __('common.subject')]));
    }

    public function edit(Subject $subject)
    {
        $levels = Level::orderBy('order')->get();
        $teachers = Teacher::orderBy('name')->get();
        return Inertia::render('Admin/Subjects/Edit', [
            'subject' => $subject->load('materials', 'exams'),
            'levels' => $levels,
            'teachers' => $teachers
        ]);
    }

    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        $data = $request->validated();

        $subject->update($data);

        if ($request->hasFile('cover')) {
            $subject->clearMediaCollection('cover'); // Remove old cover
            $subject->addMediaFromRequest('cover')->toMediaCollection('cover');
            $subject = $subject->fresh();
            $coverUrl = $subject->cover_url; // accessor
        }


        // return redirect()->route('admin.subjects.index')->with('success', 'Subject updated.');
        return redirect()->route('admin.subjects.index')->with('success', __('messages.item_updated', ['item' => __('common.subject')]));
    }

    public function destroy(Subject $subject)
    {
        // cascade deletes will handle materials & exams
        $subject->delete();

        // return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted.');
        return redirect()->route('admin.subjects.index')->with('success', __('messages.item_deleted', ['item' => __('common.subject')]));
    }
}
