<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $query = Teacher::query();

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $teachers = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers,
            'filters' => ['q' => $q]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Teachers/Create');
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        unset($data['photo']);

        $teacher = Teacher::create($data);

        if ($request->hasFile('photo')) {
            $teacher->addMediaFromRequest('photo')->toMediaCollection('photo');
            $teacher = $teacher->fresh();
            $coverUrl = $teacher->photo_url;
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function edit(Teacher $teacher)
    {
        return Inertia::render('Admin/Teachers/Edit', ['teacher' => $teacher]);
    }

    public function update(StoreTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();
        unset($data['photo']);

        $teacher->update($data);

        // Handle Photo Removal
        if ($request->boolean('remove_photo')) {
            $teacher->clearMediaCollection('photo');
        }

        if ($request->hasFile('photo')) {
            $teacher->clearMediaCollection('photo'); // Remove old cover
            $teacher->addMediaFromRequest('photo')->toMediaCollection('photo');
            $teacher = $teacher->fresh();
            $coverUrl = $teacher->photo_url;
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        // if ($teacher->photo_path) {
        //     Storage::disk('public')->delete($teacher->photo_path);
        // }
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
