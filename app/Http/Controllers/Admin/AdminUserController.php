<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = User::role('admin');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $admins = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Admins/Index', [
            'admins' => $admins,
            'filters' => ['q' => $q]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Admins/Create');
    }

    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'is_approved' => true, // Admins created by admins are auto-approved
            'level_id' => null,    // Admins don't need levels
        ]);

        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم إنشاء المسؤول بنجاح.');
    }

    public function edit(User $admin)
    {
        return Inertia::render('Admin/Admins/Edit', [
            'admin' => $admin
        ]);
    }

    public function update(StoreAdminRequest $request, User $admin)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->name = $data['name'];
        $admin->email = $data['email'] ?? null;
        $admin->phone = $data['phone'];
        $admin->save();

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم تحديث بيانات المسؤول.');
    }

    public function destroy(Request $request, User $admin)
    {
        if ($admin->id === $request->user()->id) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم حذف المسؤول.');
    }
}
