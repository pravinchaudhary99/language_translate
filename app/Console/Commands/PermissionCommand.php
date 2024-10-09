<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class PermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permission {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permission = $this->argument('permission');
        if($permission) {
            $permissionNames = collect(['list', 'create', 'edit', 'delete'])
                ->map(fn($action) => [
                    'name' => Str::lower($permission) . "-$action",
                    'group' => ucfirst(Str::lower($permission)),
                ])->toArray();
    
            Permission::insertOrIgnore($permissionNames);
    
            $role = Role::firstOrCreate(['name' => 'Super Admin', 'is_admin' => 1]);
    
            $permissionIds = Permission::pluck('id')->toArray();
    
            $role->permissions()->sync($permissionIds);
            $this->info('The permission was created successful!');
        }
    }
}
