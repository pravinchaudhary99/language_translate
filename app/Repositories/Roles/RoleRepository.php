<?php

namespace App\Repositories\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Repositories\Roles\RoleInterface;

class RoleRepository implements RoleInterface
{
    protected $responsesData = array();

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index() {
        $roles = Role::query()
                    ->whereNot('id', auth()->user()->role->id)
                    ->where('is_admin', '!=', 1)
                    ->with('permissions', 'users')
                    ->get();
        
        $permissions = Permission::get()->groupBy('group');

        foreach ($roles as $role) {
            $groupedPermissions = [];
            $userPermissions = $role->permissions->pluck('name');
            foreach ($permissions as $groupName => $groupPermissions) {
                $allPermissionsExist = $groupPermissions->every(function ($permission) use ($userPermissions) {
                    return $userPermissions->contains($permission->name);
                });
            
                if ($allPermissionsExist) {
                    $groupedPermissions[] = "All {$groupName} Controls";
                }else{
                    $specificPermissions = $groupPermissions->filter(function ($permission) use ($userPermissions) {
                        return $userPermissions->contains($permission->name);
                    });
                    
                    $permissionNames = $specificPermissions->pluck('name')->map(fn($permission) => last(explode('-', $permission)));

                    if ($permissionNames->isNotEmpty()) {
                        $count = $permissionNames->count();
            
                        $message = match ($count) {
                            1 => "View {$groupName}",
                            2 => implode(' and ', $permissionNames->toArray()) . " {$groupName}",
                            default => implode(', ', $permissionNames->slice(0, -1)->toArray()) . " and " . $permissionNames->last() . " {$groupName}",
                        };
            
                        $groupedPermissions[] = $message;
                    }
                }
            }
            $role->permissionsList = $groupedPermissions;
        }

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = ['roles' => $roles, 'permissions' => $permissions];
        return $this->responsesData;
    }

    public function store() {
        $name = $this->request->name;
        $permissions = $this->request->permissions;

        $role = Role::create(['name' => ucfirst($name)]);
        $role->permissions()->sync($permissions);
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }

    public function update($id) {
        $name = $this->request->name;
        $permissions = $this->request->permissions;
        $role = Role::find($id);
        
        if(!$role){
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        $role->update(['name' => ucfirst($name)]);
        $role->permissions()->sync($permissions);
        
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }
}