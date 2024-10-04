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
                    ->with('permissions', 'users')
                    ->get();

        $permissions = Permission::get()->groupBy('group');

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = ['roles' => $roles, 'permissions' => $permissions];
        return $this->responsesData;
    }
}