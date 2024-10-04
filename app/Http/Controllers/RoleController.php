<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Roles\RoleInterface;
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
            return view('roles.index');
        }
    }

    public function create() {
        return view('roles.index');
    }
}
