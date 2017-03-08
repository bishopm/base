<?php

namespace Bishopm\Connexion\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Bishopm\Connexion\Models\User;
use Bishopm\Connexion\Models\Role;
use Bishopm\Connexion\Repositories\UsersRepository;
use Bishopm\Connexion\Repositories\IndividualsRepository;
use Bishopm\Connexion\Repositories\RolesRepository;
use Bishopm\Connexion\Http\Requests\CreateUserRequest;
use Bishopm\Connexion\Http\Requests\UpdateUserRequest;

class UsersController extends Controller {

	private $user,$individuals;

	public function __construct(UsersRepository $user, IndividualsRepository $individuals, RolesRepository $roles)
    {
        $this->user = $user;
        $this->individuals = $individuals;
        $this->roles = $roles;
    }

	public function index()
	{
        $users = $this->user->all();
   		return view('connexion::users.index',compact('users'));
	}

	public function edit(User $user)
    {
        $uroles= $user->roles()->get();
        $data['userroles']=array();
        foreach ($uroles as $ur){
            $data['userroles'][]=$ur->id;
        }
        $data['roles']=$this->roles->all();
        $data['individuals'] = $this->individuals->all();
        $data['user'] = $user;
        return view('connexion::users.edit', $data);
    }

	public function show($user)
	{
		if ($user=="current"){
			$data['user']=User::find(1);
		} else {
			$data['user']=User::find($user);
		}
		return view('connexion::users.show',$data);
	}	

    public function create()
    {
        $data['roles']=$this->roles->all();
        $data['individuals'] = $this->individuals->all();

        return view('connexion::users.create',$data);
    }

    public function store(CreateUserRequest $request)
    {
        $user=User::create($request->except('password','role_id'));
        if ($request->input('password')<>""){
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
        $user->roles()->attach($request->role_id);
        return redirect()->route('admin.users.index')
            ->withSuccess('New user added');
    }
	
    public function update($user, UpdateUserRequest $request)
    {
        $user=User::find($user);
        $user->fill($request->except('password','role_id'));
        if ($request->input('password')<>""){
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
        $user->roles()->detach();
        $user->roles()->attach($request->role_id);        
        return redirect()->route('admin.users.index')->withSuccess('User has been updated');
    }


}
 