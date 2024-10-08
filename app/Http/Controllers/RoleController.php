<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Repositories\Roles\RoleInterface;
use App\Http\Requests\Admin\RoleUpdateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RoleController extends Controller implements HasMiddleware
{
    protected $repo;

    public function __construct(RoleInterface $interface) {
        $this->repo = $interface;
    }

   public static function middleware(): array
    {
        return [
            new Middleware('permissions:role-list,role-create,role-edit', only: ['index']),
        ];
    }

    public function index() {
        try {
            $responses = $this->repo->index();

            $data = $responses['data'];
            return view('roles.index', compact('data'));
        } catch (\Exception $e) {
            return view('roles.index', compact(['error' => $e->getMessage()]));
        }
    }

    public function store(RoleRequest $request) {
        try {
            $this->repo->store();
            return successResponses(__('messages.role_created'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function update(RoleUpdateRequest $request, $id) {
        try {
            $responses = $this->repo->update($id);

            if(!$responses['success']){
                return errorResponses(__('messages.role_not_found'));
            }

            return successResponses(__('messages.role_updated'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }
}
