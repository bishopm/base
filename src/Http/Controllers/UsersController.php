<?php

namespace Bishopm\Connexion\Http\Controllers;

use App\Http\Controllers\Controller, MediaUploader;
use Bishopm\Connexion\Models\User;
use Bishopm\Connexion\Models\Role;
use Bishopm\Connexion\Models\Household;
use Bishopm\Connexion\Repositories\UsersRepository;
use Bishopm\Connexion\Repositories\IndividualsRepository;
use Bishopm\Connexion\Http\Requests\CreateUserRequest;
use Bishopm\Connexion\Http\Requests\UpdateUserRequest;
use Bishopm\Connexion\Notifications\ProfileUpdated;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use Auth;

class UsersController extends Controller {

	private $user,$individuals;

	public function __construct(UsersRepository $user, IndividualsRepository $individuals)
    {
        $this->user = $user;
        $this->individuals = $individuals;
    }

	public function index()
	{
        $users = $this->user->all();
   		return view('connexion::users.index',compact('users'));
	}

    public function activate()
    {
        $users = $this->user->inactive();
        return view('connexion::users.activate',compact('users'));
    }

    public function activateuser($id)
    {
        $user=$this->user->activate($id);
        $hid=$user->individual->household_id;
        $household=Household::withTrashed()->where('id',$hid)->first();
        $household->restore();
        UserVerification::generate($user);
        UserVerification::send($user, 'Welcome!');
        return redirect()->route('admin.users.activate')
            ->withSuccess('User has been activated');
    }

	public function edit(User $user)
    {
        $uroles= $user->roles;
        $data['userroles']=array();
        foreach ($uroles as $ur){
            $data['userroles'][]=$ur->id;
        }
        $data['roles']=Role::all();
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
        $data['roles']=Role::all();
        $data['individuals'] = $this->individuals->all();

        return view('connexion::users.create',$data);
    }

    public function store(CreateUserRequest $request)
    {
        $user=User::create($request->except('password','role_id'));
        if ($request->input('password')<>""){
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();
        $user->roles()->attach($request->role_id);
        return redirect()->route('admin.users.index')
            ->withSuccess('New user added');
    }
	
    public function update($user, UpdateUserRequest $request)
    {
        $user=User::find($user);
        if (null!==$request->input('profile')){
            $individual=$user->individual;
            $fname=$individual->id;
            $individual->service_id=$request->input('service_id');
            $individual->image=$request->input('image');
            $individual->save();
            $user->bio=$request->input('bio');
            $user->save();
            //$user->notify(new ProfileUpdated($user));
            return redirect()->route('webuser.edit',$individual->slug)->withSuccess('User profile has been updated');
        } else {
            $user->fill($request->except('password','role_id','profile'));
            if ($request->input('password')<>""){
                $user->password = bcrypt($request->input('password'));
            }
            $user->save();
            $user->roles()->detach();
            $user->roles()->attach($request->role_id);        
            //$user->notify(new ProfileUpdated($user));
            return redirect()->route('admin.users.index')->withSuccess('User has been updated');
        }
    }


}
 