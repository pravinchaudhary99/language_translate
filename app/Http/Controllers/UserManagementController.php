<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserRequest;
use Illuminate\Http\Request;
use App\Repositories\Users\UserInterface;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserManagementController extends Controller implements HasMiddleware
{
    protected $repo;

    public function __construct(UserInterface $interface) {
        $this->repo = $interface;
    }

   public static function middleware(): array
    {
        return [
            new Middleware('permissions:user-list,user-create,user-edit', only: ['index', 'list']),
        ];
    }

    public function index() {
        try {
            $responses = $this->repo->index();

            $roles = $responses['roles'];
            return view('users.index', compact('roles'));
        } catch (\Exception $e) {
            return view('users.index');
        }
    }

    public function list() {
        try {
            $responses = $this->repo->list();

            return responseDataTable($responses['data']);
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function store(UserRequest $request) {
        try {
            $this->repo->store();

            return successResponses(__('messages.user_created'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function edit($id) {
        try {
            $responses = $this->repo->edit($id);

            $data = $responses['data'];
            return successResponses(__('messages.user_found'), $data);
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }
}
