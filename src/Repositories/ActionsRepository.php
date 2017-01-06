<?php namespace bishopm\base\Repositories;

use bishopm\base\Repositories\EloquentBaseRepository;

class ActionsRepository extends EloquentBaseRepository
{
  public function all()
  {
      return $this->model->with('project')->where('completed',0)->orderBy('description')->get();
  }

  public function findbytid($tid,$uid){
      return $this->model->where('toodledo_id','=',$tid)->where('user_id','=',$uid)->first();
  }

  public function findbyuid($uid){
        return $this->model->where('user_id','=',$uid)->get();
  }

  public function incomplete($limit,$user){
      return $this->model->where('user_id','=',$user)->whereNull('completed')->get()->take($limit);
  }

}
