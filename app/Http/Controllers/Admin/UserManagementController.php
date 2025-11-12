<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Module;
use App\Models\ModuleAccess;
use App\Models\UserGroupPermission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display users list.
     */
    public function index(): View
    {
        $users = User::with('userGroup')->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'pageTitle' => 'Manajemen Pengguna',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Pengguna', 'url' => ''],
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $userGroups = UserGroup::all();
        $modules = Module::with('menu')->where('is_active', true)->orderBy('row_order')->get();
        $moduleAccesses = ModuleAccess::all()->groupBy('module_id');

        return view('admin.users.create', [
            'userGroups' => $userGroups,
            'modules' => $modules,
            'moduleAccesses' => $moduleAccesses,
            'pageTitle' => 'Tambah Pengguna',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Pengguna', 'url' => route('admin.users.index')],
                ['label' => 'Tambah Pengguna', 'url' => ''],
            ],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_group_id' => 'required|exists:user_groups,id',
            'permissions' => 'sometimes|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_group_id' => $request->user_group_id,
        ]);

        // Handle permissions
        if ($request->has('permissions')) {
            $this->updateUserGroupPermissions($request->user_group_id, $request->permissions);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $userGroups = UserGroup::all();
        $modules = Module::with('menu')->where('is_active', true)->orderBy('row_order')->get();
        $moduleAccesses = ModuleAccess::all()->groupBy('module_id');

        // Get current user group permissions
        $currentPermissions = [];
        if ($user->userGroup) {
            $currentPermissions = $user->userGroup->permissions()
                ->pluck('status', 'module_access_id')
                ->toArray();
        }

        return view('admin.users.edit', [
            'user' => $user,
            'userGroups' => $userGroups,
            'modules' => $modules,
            'moduleAccesses' => $moduleAccesses,
            'currentPermissions' => $currentPermissions,
            'pageTitle' => 'Edit Pengguna',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Pengguna', 'url' => route('admin.users.index')],
                ['label' => 'Edit Pengguna', 'url' => ''],
            ],
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'user_group_id' => 'required|exists:user_groups,id',
            'permissions' => 'sometimes|array',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'user_group_id' => $request->user_group_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        // Handle permissions
        if ($request->has('permissions')) {
            $this->updateUserGroupPermissions($request->user_group_id, $request->permissions);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Update user group permissions
     */
    private function updateUserGroupPermissions($userGroupId, $permissions)
    {
        // Delete existing permissions for this user group
        UserGroupPermission::where('user_group_id', $userGroupId)->delete();

        // Create new permissions
        foreach ($permissions as $moduleAccessId => $status) {
            if ($status) {
                UserGroupPermission::create([
                    'user_group_id' => $userGroupId,
                    'module_access_id' => $moduleAccessId,
                    'status' => true,
                ]);
            }
        }
    }
}
