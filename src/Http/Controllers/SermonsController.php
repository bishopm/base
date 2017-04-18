<?php

namespace Bishopm\Connexion\Http\Controllers;

use Bishopm\Connexion\Repositories\SermonsRepository;
use Bishopm\Connexion\Repositories\UsersRepository;
use Bishopm\Connexion\Models\Sermon;
use Bishopm\Connexion\Models\Individual;
use App\Http\Controllers\Controller;
use Bishopm\Connexion\Http\Requests\CreateCommentRequest;
use Bishopm\Connexion\Http\Requests\CreateSermonRequest;
use Bishopm\Connexion\Http\Requests\UpdateSermonRequest;

class SermonsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	private $sermon,$user;

	public function __construct(SermonsRepository $sermon, UsersRepository $user)
    {
        $this->sermon = $sermon;
        $this->user = $user;
    }

	public function edit($series,Sermon $sermon)
    {
        $data['tags']=Sermon::allTags()->get();
        $data['btags']=array();
        foreach ($sermon->tags as $tag){
            $data['btags'][]=$tag->name;
        }
        $data['preachers'] = Individual::withTag('preacher')->get();
        $data['series'] = $series;
        $data['sermon'] = $sermon;
        return view('connexion::sermons.edit', $data);
    }

    public function create($series_id)
    {
        $data['tags']=Sermon::allTags()->get();
        $data['preachers']= Individual::withTag('preacher')->get();
        $data['series_id']=$series_id;
        return view('connexion::sermons.create',$data);
    }

	public function show(Sermon $sermon)
	{
        $data['sermon']=$sermon;
        return view('connexion::sermons.show',$data);
	}

    public function store(CreateSermonRequest $request)
    {
        $sermon=$this->sermon->create($request->except('tags'));
        $sermon->tag($request->tags);
        return redirect()->route('admin.series.show',$request->series_id)
            ->withSuccess('New sermon added');
    }
	
    public function update($series, Sermon $sermon, UpdateSermonRequest $request)
    {
        $this->sermon->update($sermon,$request->except('tags'));
        $sermon->tag($request->tags);
        return redirect()->route('admin.series.show',$series)->withSuccess('Sermon has been updated');
    }

    public function addcomment($series, Sermon $sermon, CreateCommentRequest $request)
    {
        $user=$this->user->find($request->user);
        $user->comment($sermon, $request->newcomment);
    }

}