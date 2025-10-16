<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $query = Subject::with('level', 'creator')->withCount(['materials', 'exams']);

        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }

        $subjects = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $subjects,
            'filters' => ['q' => $q],
        ]);
    }

    public function create()
    {
        $levels = Level::orderBy('order')->get();
        return Inertia::render('Admin/Subjects/Create', ['levels' => $levels]);
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

        return Inertia::render('Admin/Subjects/Edit', [
            'subject' => $subject->load('materials', 'exams'),
            'levels' => $levels,
        ]);
    }

    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        dd($request->all(), $request->files->all());

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
