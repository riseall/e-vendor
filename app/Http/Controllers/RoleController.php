<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Map permissions into logical module groups.
     *
     * @return array
     */
    private function getGroupedPermissions()
    {
        $permissions = Permission::all();
        $grouped = [
            'User Management' => [],
            'Master Role & Permission' => [],
            'Verifikasi Pendaftaran' => [],
            'QA Risk Assessment' => [],
            'Audit Vendor' => [],
            'Rekualifikasi Vendor' => [],
            'Master Questionnaire' => [],
            'Evaluasi Vendor' => [],
            'Lainnya' => []
        ];

        foreach ($permissions as $permission) {
            $name = $permission->name;
            if (strpos($name, 'user-') === 0) {
                $grouped['User Management'][] = $permission;
            } elseif (strpos($name, 'role-') === 0) {
                $grouped['Master Role & Permission'][] = $permission;
            } elseif (strpos($name, 'verifikasi-') === 0) {
                $grouped['Verifikasi Pendaftaran'][] = $permission;
            } elseif (strpos($name, 'qa-risk-') === 0) {
                $grouped['QA Risk Assessment'][] = $permission;
            } elseif (strpos($name, 'audit-') === 0) {
                $grouped['Audit Vendor'][] = $permission;
            } elseif (strpos($name, 'rekualifikasi-') === 0) {
                $grouped['Rekualifikasi Vendor'][] = $permission;
            } elseif (strpos($name, 'questionnaire-') === 0) {
                $grouped['Master Questionnaire'][] = $permission;
            } elseif (strpos($name, 'evaluasi-') === 0) {
                $grouped['Evaluasi Vendor'][] = $permission;
            } else {
                $grouped['Lainnya'][] = $permission;
            }
        }

        return array_filter($grouped, function ($items) {
            return !empty($items);
        });
    }

    /**
     * Display a listing of roles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        $groupedPermissions = $this->getGroupedPermissions();

        return view('admin.role.index', compact('roles', 'groupedPermissions'));
    }

    /**
     * Store a newly created role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan.',
                'data' => $role
            ]);
        }

        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Get assigned permission IDs for a specific role (AJAX).
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions(Role $role)
    {
        $assignedPermissions = $role->permissions()->pluck('name')->toArray();

        return response()->json([
            'success' => true,
            'role' => $role,
            'assigned' => $assignedPermissions
        ]);
    }

    /**
     * Update permissions assigned to a role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $permissions = $request->input('permissions', []);
        
        // Use spatie's syncPermissions method
        $role->syncPermissions($permissions);

        // Reset permission cache immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permission untuk role "' . $role->name . '" berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin IT'])) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role utama sistem tidak dapat dihapus.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Role utama sistem tidak dapat dihapus.');
        }

        $role->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}
