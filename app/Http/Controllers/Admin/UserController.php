<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $pendingUsers = User::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Students/Index', [
            'pending' => $pendingUsers,
        ]);
    }

    public function approve(User $user)
    {
        $was_denied = !is_null($user->denied_at);

        if (!$was_denied && is_null($user->level_id)) {
            $first = \App\Models\Level::orderBy('order')->first();
            if ($first)
                $user->level_id = $first->id;
        }

        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'تم قبول المستخدم.');
    }

    public function deny(User $user)
    {
        $user->is_approved = false;
        $user->denied_at = now();
        $user->save();

        return back()->with('success', 'تم رفض المستخدم.');
    }
}
