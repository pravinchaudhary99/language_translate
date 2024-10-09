<?php

namespace App\Repositories\Users;

interface UserInterface
{
    public function index();
    
    public function list();

    public function store();

    public function edit($id);

    public function update($id);

    public function destroy($id);
}