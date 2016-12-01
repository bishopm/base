<?php

namespace bishopm\base\Http\Controllers;

use Auth;
use bishopm\base\Http\Controllers\Toodledo;
use bishopm\base\Models\Action;
use bishopm\base\Models\User;
use bishopm\base\Repositories\UsersRepository;
use bishopm\base\Repositories\ActionsRepository;
use bishopm\base\Repositories\IndividualsRepository;
use bishopm\base\Repositories\ProjectsRepository;
use bishopm\base\Repositories\FoldersRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class ActionsController extends Controller
{
    /**
     * @var ActionRepository
     */
    private $action, $individuals, $projects, $folders, $user;

    public function __construct(ActionsRepository $action, IndividualsRepository $individuals, ProjectsRepository $projects, FoldersRepository $folders, UsersRepository $user)
    {
        $this->action = $action;
        $this->individuals = $individuals;
        $this->projects = $projects;
        $this->folders = $folders;
        $this->user = $user;
        $this->provider = new Toodledo();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        dd($currentUser);
        $user_id=$this->user->id();
        $userextension = $userextension->find($user_id);
        if (is_null($userextension) or (isset($_GET['code']))) {
            $authorizationUrl = $this->provider->getAuthorizationUrl();
            if (isset($_GET['code'])){
                $token=$this->provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
                $tokenStr=$token->getToken();
                $data = $this->provider->getData($tokenStr,'account','initial');
                $userextension=UserExtension::create(['user_id'=>$user_id, 'toodledo_id'=>$data->userid, 'toodledo_token'=>$tokenStr,'toodledo_refresh'=>$token->getRefreshToken()]);
                Artisan::call('toodledo:sync', ['category' => 'initial']);
            }
        } else {
            $authorizationUrl = "NA";
        }
        $actions = $this->action->all();
        return view('todo::admin.actions.index', compact('actions','authorizationUrl'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $individuals=$this->individuals->dropdown();
        $folders=$this->folders->dropdown();
        $projects=$this->projects->dropdown();
        return view('todo::admin.actions.create',compact('individuals','projects','folders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->action->create($request->all());

        return redirect()->route('admin.todo.action.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('todo::actions.title.actions')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Action $action
     * @return Response
     */
    public function edit(Action $action)
    {
        $individuals=$this->individuals->dropdown();
        $folders=$this->folders->dropdown();
        $projects=$this->projects->dropdown();
        return view('todo::admin.actions.edit', compact('action','individuals','projects','folders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Action $action
     * @param  Request $request
     * @return Response
     */
    public function update(Action $action, Request $request)
    {
        $this->action->update($action, $request->all());

        return redirect()->route('admin.todo.action.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('todo::actions.title.actions')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Action $action
     * @return Response
     */
    public function destroy(Action $action)
    {
        $this->action->destroy($action);

        return redirect()->route('admin.todo.action.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('todo::actions.title.actions')]));
    }
}
