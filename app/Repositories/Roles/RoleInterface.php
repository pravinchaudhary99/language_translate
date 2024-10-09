<?php

namespace App\Repositories\Roles;

interface RoleInterface
{
    public function index();

    public function store();

    public function update($id);

    public function view($id);

    public function list($id);
}
