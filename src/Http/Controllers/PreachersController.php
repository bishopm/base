<?php

namespace Bishopm\Connexion\Http\Controllers;

use Bishopm\Connexion\Repositories\PreachersRepository;
use Bishopm\Connexion\Repositories\IndividualsRepository;
use Bishopm\Connexion\Repositories\SocietiesRepository;
use Bishopm\Connexion\Repositories\SettingsRepository;
use App\Http\Controllers\Controller;
use Bishopm\Connexion\Http\Requests\CreatePreacherRequest;
use Bishopm\Connexion\Http\Requests\UpdatePreacherRequest;

class PreachersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $preacher;
    private $individuals;
    private $societies;
    private $settings;

    public function __construct(PreachersRepository $preacher, IndividualsRepository $individuals, SocietiesRepository $societies, SettingsRepository $settings)
    {
        $this->preacher = $preacher;
        $this->individuals = $individuals;
        $this->societies = $societies;
        $this->settings = $settings;
    }

    public function index()
    {
        $preachers = $this->preacher->all();
        if ($preachers=="No valid url") {
            return redirect()->route('admin.settings.index')->with('notice', 'Please ensure that the API url is correctly specified');
        } else {
            return view('connexion::preachers.index', compact('preachers'));
        }
    }

    public function edit($id)
    {
        //$data['individuals'] = $this->individuals->all();
        $data['circuit'] = $this->settings->getkey('circuit');
        $data['societies'] = $this->societies->all();
        $data['preacher']=$this->preacher->find($id);
        return view('connexion::preachers.edit', $data);
    }

    public function create()
    {
        $data['individuals'] = $this->individuals->all();
        $data['societies'] = $this->societies->all();
        $data['circuit'] = $this->settings->getkey('circuit');
        if (count($data['societies'])) {
            return view('connexion::preachers.create', $data);
        } else {
            return redirect()->route('admin.societies.create')->with('notice', 'At least one society must be added before adding a preacher');
        }
    }

    public function show($id)
    {
        $data['preacher']=$this->preacher->find($id);
        return view('connexion::preachers.show', $data);
    }

    public function store(CreatePreacherRequest $request)
    {
        $this->preacher->create($request->except('image', 'token'));

        return redirect()->route('admin.preachers.index')
            ->withSuccess('New preacher added');
    }
    
    public function update($id, UpdatePreacherRequest $request)
    {
        $this->preacher->update($id, $request->except('image', 'token'));
        return redirect()->route('admin.preachers.index')->withSuccess('Preacher has been updated');
    }
}
