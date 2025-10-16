<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('name')->paginate(15);
        return Inertia::render('Admin/Teachers/Index', ['teachers' => $teachers]);
    }

    public function create()
    {
        return Inertia::render('Admin/Teachers/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        // if ($request->hasFile('photo')) {
        //     $data['photo_path'] = $request->file('photo')->store('teachers', 'public');
        // }

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

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        // if ($request->hasFile('photo')) {
        //     // Delete old photo if it exists
        //     if ($teacher->photo_path) {
        //         Storage::disk('public')->delete($teacher->photo_path);
        //     }
        //     $data['photo_path'] = $request->file('photo')->store('teachers', 'public');
        // }

        $teacher->update($data);

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
        if ($teacher->photo_path) {
            Storage::disk('public')->delete($teacher->photo_path);
        }
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}