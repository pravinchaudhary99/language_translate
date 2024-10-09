<?php

namespace App\Repositories\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Users\UserInterface;

class UserRepository implements UserInterface
{
    protected $responsesData = array();

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index() {
        $roles = Role::query()
                    ->where('is_admin', '!=', 1)
                    ->get();

        $this->responsesData['success'] = true;
        $this->responsesData['roles'] = $roles;
        return $this->responsesData;
    }

    public function list() {
        $orderBy = $this->request->order[0]['column'];
        $direction = $this->request->order[0]['dir'];
        $skip = $this->request->start;
        $take = $this->request->length;
        $searchValue = $this->request->search['value'];
        $columns = ['id', 'name', 'email'];

        $users = User::query()
                ->with('role')
                ->whereNot('id', auth()->user()->id)
                ->whereHas('role', function($query) {
                    $query->where('is_admin', '!=', 1);
                })
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

    public function store() {
        User::create([
            'name' => $this->request->name,
            'email' => $this->request->email,
            'password' => Hash::make($this->request->password),
            'role_id' => $this->request->role,
        ]);

        $this->responsesData['success'] = true;
        return $this->responsesData;
    }

    public function edit($id) {
        $user = User::find($id);

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = ['user' => $user];
        return $this->responsesData;
    }

    public function update($id) {
        $user = User::find($id);

        if(!$user) {
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        if($this->request->password && $this->request->password != ''){
            $data = [
                'name' => $this->request->name,
                'email' => $this->request->email,
                'password' => Hash::make($this->request->password),
                'role_id' => $this->request->role,
            ];
        }else{
            $data = [
                'name' => $this->request->name,
                'email' => $this->request->email,
                'role_id' => $this->request->role,
            ];
        }
        $user->update($data);

        $this->responsesData['success'] = true;
        return $this->responsesData;
    }

    public function destroy($id) {
        $user = User::find($id);

        if(!$user) {
            $this->responsesData['success'] = false;
            return $this->responsesData;
        }

        $user->delete();
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }
}