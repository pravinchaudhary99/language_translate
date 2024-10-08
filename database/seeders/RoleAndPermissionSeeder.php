<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $permissions = [
        ['name' => 'role-list', 'group' => 'Role'],
        ['name' => 'role-create', 'group' => 'Role'],
        ['name' => 'role-edit', 'group' => 'Role'],
        ['name' => 'role-delete', 'group' => 'Role'],
        ['name' => 'language-list', 'group' => 'Language'],
        ['name' => 'language-create', 'group' => 'Language'],
        ['name' => 'language-edit', 'group' => 'Language'],
        ['name' => 'language-delete', 'group' => 'Language'],
        ['name' => 'translate-list', 'group' => 'Translate'],
        ['name' => 'translate-create', 'group' => 'Translate'],
        ['name' => 'translate-edit', 'group' => 'Translate'],
        ['name' => 'translate-delete', 'group' => 'Translate'],
    ];

    public function run()
    {
        // Create permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name'], 'group' => $permission['group']]);
        }

        // Create roles
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'is_admin' => 1]);

        $permissionIds = Permission::pluck('id')->toArray();

        $role->permissions()->sync($permissionIds);

        $user = User::where('email','admin@gmail.com')->first();
        if ($user) {
            $user->update(['role_id' => $role->id]);
        }
    }
}
