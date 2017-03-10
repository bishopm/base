<?php namespace Bishopm\Connexion\Repositories;

use Bishopm\Connexion\Repositories\EloquentBaseRepository;

class UsersRepository extends EloquentBaseRepository
{
	public function getidbytoodledo($toodledo_id){
        return $this->model->where('toodledo_id','=',$toodledo_id)->get();
    }

	public function getuserbyindiv($individual_id){
        return $this->model->where('individual_id','=',$individual_id)->first();
    }    
}