<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all();
        $users = User::orderBy('name')->get();

        return view('roles.index', compact('roles', 'permissions', 'users'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $users = User::orderBy('name')->get();
        $roleUsers = $role->users->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'users', 'roleUsers'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($role->isAdmin()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل صلاحيات المدير. المدير لديه جميع الصلاحيات تلقائياً.');
        }

        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'تم تحديث الصلاحيات بنجاح');
    }

    public function assignUser(Request $request, Role $role)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        if (!$role->users->contains($user->id)) {
            $role->users()->attach($user->id);
        }

        return redirect()->back()->with('success', 'تم تعيين الدور للمستخدم بنجاح');
    }

    public function removeUser(Role $role, User $user)
    {
        $role->users()->detach($user->id);

        return redirect()->back()->with('success', 'تم إزالة الدور من المستخدم بنجاح');
    }

    public function permissions()
    {
        $permissions = Permission::all();

        return view('roles.permissions', compact('permissions'));
    }
}
