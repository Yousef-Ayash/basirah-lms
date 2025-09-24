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

        return Inertia::render('Admin/Users/Index', [
            'pending' => $pendingUsers,
        ]);
    }

    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->save();

        // optionally notify the user here (email/notification)

        // return back()->with('success', 'User approved.');
        return back()->with('success', __('messages.user_approved'));
    }

    public function deny(User $user)
    {
        // or delete or set a flag
        $user->delete();

        // return back()->with('success', 'User deleted.');
        return back()->with('success', __('messages.user_deleted'));
    }
}
