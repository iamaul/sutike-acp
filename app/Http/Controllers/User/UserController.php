<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Request as UserRequest;

class UserController extends Controller
{
    public function __construct(User $user, Role $roles)
    {
    	parent::__construct();
        $this->user = $user;
    	$this->roles = $roles;
    }

    public function index(UserRequest $request)
    {
    	if ($request->ajax()) {
            return app('datatables')->eloquent($this->user->ofUser())
                ->addColumn('roles', function (User $user) {
                    return $user->roles->map(function($roles) {
                        return ucwords($roles->name);
                    })->implode('[]');
                })
                ->addColumn('action', __v().'.users.datatables.action')
                ->editColumn('name', '{{ ucwords($name) }}')
                ->editColumn('email', '{{ email($email) }}')
                ->editColumn('avatar', '<img src="{{ $avatar }}" alt="{{ ucwords($name) }}" class="img-responsive avatar-on-table">')
                ->rawColumns(['avatar', 'action'])
                ->orderColumns(['name', 'email', 'roles.name'], ':column $1')
                ->make(true);
        }
    	return view("{$this->view}::users.index", [
            'roles' => $this->roles->ofRoles()->get(),
            'users' => $this->user->ofUser()->paginate(6)
        ]);
    }

    public function resetPass(UserRequest $request, $id)
    {
        $data = $this->user->ofUser($id);
        return view("{$this->view}::users.resetPassword", ['data' => $data]);
    }


    public function updatePass(UserRequest $request, $id)
    {   
        $user = $this->user->findOrFail($id);
        if ($request->ajax()) {       
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->successResponse(microtime_float(), $user, 'Password updated successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to update password');
    }


    public function create(UserRequest $request)
    {
        if ($request->ajax()) {
            if (request()->has('id')) {
                $user = $this->user->where('email', request('email'))->where('id', '<>', request('id'))->first();
            } else {
                $user = $this->user->where(['email' => request('email')])->first();
            }
            return response()->json(
                [
                    'valid' => $user ? false : true
                ]
            );
        }
    }

    public function show(UserRequest $request, $load)
    {
        if ($request->ajax()) {
            $result = $this->{$load}->select("id","name as text")->where("name", 'LIKE', "{$request->get('query')}%")->where('name', '!=', get_json_user()['default_role'])->get();
            return response()->successResponse(microtime_float(), $result);
        }
    }

    public function store(UserRequest $request)
    {
        if ($request->ajax()) {
            $user = $this->user->create(
                collect($request->all())->merge(
                    [
                        'email_verified_at' => carbon()->today()->toDateTimeString(),
                        'password' => bcrypt($request->password)
                    ]
                )->toArray()
            );
            $user->roles()->attach([$request->roles]);
            return response()->successResponse(microtime_float(), $user, 'User created successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to create user');
    }

    public function edit(UserRequest $request, $id)
    {
        if ($request->ajax()) {
            $data = $this->user->ofUser($id);
            return response()->successResponse(microtime_float(), $data);
        }
    }

    public function update(UserRequest $request, $id)
    {
        if ($request->ajax()) {
            $user = $this->user->findOrFail($id);
            $role = $this->roles->findOrFail($request->roles);
            if (!$user || !$role) return response()->failedResponse(microtime_float());
            if ($request->password != '') {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            $user->syncRoles([]);
            $user->attachRole($role);
            return response()->successResponse(microtime_float(), $user, 'User updated successfully');
        }
        return response()->failedResponse(microtime_float(), 'Failed to update user');
    }

    public function destroy(UserRequest $request, $id)
    {
        if ($request->ajax()) {
            if ($this->user->destroy($id)) {
                return response()->successResponse(microtime_float(), [], 'User deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete user');
        }
    }

    public function destroyMany(UserRequest $request)
    {
        if ($request->ajax()) {
            $id_can_be_destroy = [];
            foreach ($request->all() as $id) {
                $user = $this->user->findOrFail($id);
                if($user) array_push($id_can_be_destroy, $id);
            }
            if ($user->destroy($id_can_be_destroy)) {
                return response()->successResponse(microtime_float(), [], 'users deleted successfully');
            }
            return response()->failedResponse(microtime_float(), 'Failed to delete users');
        }
    }
}