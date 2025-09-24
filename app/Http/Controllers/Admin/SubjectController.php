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

        // handle cover image
        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $basename = Str::uuid()->toString();
            $ext = $cover->getClientOriginalExtension();
            $filename = "{$basename}.{$ext}";
            $path = $cover->storeAs('covers', $filename, 'public');

            // generate small cover thumbnail synchronously
            try {
                $img = Image::make($cover->getRealPath());
                $img->fit(1200, 400, function ($c) {
                    $c->upsize();
                });
                $thumbPath = "covers/thumbnails/{$basename}.jpg";
                Storage::disk('public')->put($thumbPath, (string) $img->encode('jpg', 80));
            } catch (\Throwable $e) {
                \Log::warning("Cover thumbnail creation failed: " . $e->getMessage());
            }

            $data['cover_image_path'] = $path;
        }

        $subject = Subject::create($data);

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
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            // delete old
            if ($subject->cover_image_path && Storage::disk('public')->exists($subject->cover_image_path)) {
                Storage::disk('public')->delete($subject->cover_image_path);
            }
            // store new
            $cover = $request->file('cover');
            $basename = Str::uuid()->toString();
            $ext = $cover->getClientOriginalExtension();
            $filename = "{$basename}.{$ext}";
            $path = $cover->storeAs('covers', $filename, 'public');

            // create thumbnail
            try {
                $img = Image::make($cover->getRealPath());
                $img->fit(1200, 400, function ($c) {
                    $c->upsize();
                });
                $thumbPath = "covers/thumbnails/{$basename}.jpg";
                Storage::disk('public')->put($thumbPath, (string) $img->encode('jpg', 80));
            } catch (\Throwable $e) {
                \Log::warning("Cover thumbnail creation failed: " . $e->getMessage());
            }

            $data['cover_image_path'] = $path;
        }

        $subject->update($data);

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
