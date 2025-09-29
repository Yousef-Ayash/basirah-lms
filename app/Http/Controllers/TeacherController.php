<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('name')->get();
        return Inertia::render('Teachers/Index', ['teachers' => $teachers]);
    }
}