<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
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

    public function permissions()
    {
        $permissions = Permission::all();
        
        return view('roles.permissions', compact('permissions'));
    }
}
