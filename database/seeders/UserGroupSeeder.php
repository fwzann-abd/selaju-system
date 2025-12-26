<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleAccess;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User Group
        $superAdminGroup = UserGroup::create([
            'name' => 'Super Admin',
            'status' => true,
        ]);

        // Create Admin User Group
        $adminGroup = UserGroup::create([
            'name' => 'Admin',
            'status' => true,
        ]);

        // Create Editor User Group
        $editorGroup = UserGroup::create([
            'name' => 'Editor',
            'status' => true,
        ]);

        // Get all modules
        $modules = Module::all();

        // Create module accesses for each module
        foreach ($modules as $module) {
            // Create CRUD accesses for each module
            $accesses = ['view', 'create', 'edit', 'delete'];

            foreach ($accesses as $access) {
                ModuleAccess::create([
                    'module_id' => $module->id,
                    'type' => 'action',
                    'identifiers' => $module->identifiers.'-'.$access,
                    'name' => ucfirst($access).' '.$module->name,
                ]);
            }
        }

        // Get all module accesses
        $allAccesses = ModuleAccess::all();

        // Grant all permissions to Super Admin
        foreach ($allAccesses as $access) {
            UserGroupPermission::create([
                'user_group_id' => $superAdminGroup->id,
                'module_access_id' => $access->id,
                'status' => true,
            ]);
        }

        // Grant permissions to Admin (all except admin settings)
        foreach ($allAccesses as $access) {
            $isAdminOnly = in_array($access->identifiers, [
                'menu-management-view',
                'menu-management-create',
                'menu-management-edit',
                'menu-management-delete',
                'role-permission-view',
                'role-permission-create',
                'role-permission-edit',
                'role-permission-delete',
            ]);

            if (! $isAdminOnly) {
                UserGroupPermission::create([
                    'user_group_id' => $adminGroup->id,
                    'module_access_id' => $access->id,
                    'status' => true,
                ]);
            }
        }

        // Grant limited permissions to Editor (only artikel)
        foreach ($allAccesses as $access) {
            $isEditorAllowed = strpos($access->identifiers, 'artikel-list') !== false;

            if ($isEditorAllowed) {
                UserGroupPermission::create([
                    'user_group_id' => $editorGroup->id,
                    'module_access_id' => $access->id,
                    'status' => true,
                ]);
            }
        }

        // Assign Super Admin group to the dev user
        $superAdmin = User::where('email', 'dev@gncs.dev')->first();
        if ($superAdmin) {
            $superAdmin->update(['user_group_id' => $superAdminGroup->id]);
        }
    }
}
