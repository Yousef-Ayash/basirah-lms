<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $query = Course::query()->withCount('subjects');

        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }

        $courses = $query->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => ['q' => $q]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Courses/Create');
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $course = Course::create($data);

        // Handle Cover Image Upload
        if ($request->hasFile('cover')) {
            $course->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم إنشاء المقرر بنجاح.');
    }

    public function edit(Course $course)
    {
        return Inertia::render('Admin/Courses/Edit', [
            // Append cover_url accessor if not auto-appended
            'course' => $course->append('cover_url')
        ]);
    }

    public function update(StoreCourseRequest $request, Course $course)
    {
        $data = $request->validated();
        $course->update($data);

        // Handle Cover Image Removal
        if ($request->boolean('remove_cover')) {
            $course->clearMediaCollection('cover');
        }

        // Handle Cover Image Replacement
        if ($request->hasFile('cover')) {
            $course->clearMediaCollection('cover'); // Remove old cover
            $course->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم تحديث المقرر بنجاح.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم حذف المقرر بنجاح.');
    }
}
