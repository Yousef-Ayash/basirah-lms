<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ViewController extends Controller
{
    public function viewAsStudent()
    {
        session(['view_as_student' => true]);

        // Redirect to the student dashboard
        return redirect()->route('dashboard');
    }

    public function viewAsAdmin()
    {
        session()->forget('view_as_student');

        // Redirect to the admin dashboard
        return redirect()->route('dashboard');
    }
}