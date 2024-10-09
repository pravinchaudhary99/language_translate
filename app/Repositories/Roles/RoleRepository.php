<?php

namespace App\Repositories\Roles;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
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

    public function view($id) {
        $role = Role::query()
                ->where('id', $id)
                ->with('permissions', 'users')
                ->first();

        if(!$role){
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        $permissions = Permission::get()->groupBy('group');

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

        $role->permissions = $groupedPermissions;

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = [
            'role' => $role,
            'users' => $role->users
        ];
        return $this->responsesData;
    }

    public function list($id){
        $orderBy = $this->request->order[0]['column'];
        $direction = $this->request->order[0]['dir'];
        $skip = $this->request->start;
        $take = $this->request->length;
        $searchValue = $this->request->search['value'];
        $columns = ['id', 'name', 'email'];

        $users = User::query()
                ->where('role_id', $id)
                ->orderBy($columns[$orderBy], $direction);

        $recordsTotal = $users->count();

        if ($searchValue) {
            $users->whereAny(['name', 'email'], 'like', '%' . $searchValue . '%');
        }

        $recordsFiltered = $users->count();

        $users = $users->skip($skip)->take($take)->get();
        $this->responsesData['data'] = [
            'data' => isset($users) ? $users : [],
            'recordsTotal' => isset($recordsTotal) ? $recordsTotal : 0,
            'recordsFiltered' => isset($recordsFiltered) ? $recordsFiltered : 0,
        ];
        return $this->responsesData;
    }
}