<?php namespace bishopm\base\Repositories;

use bishopm\base\Repositories\EloquentBaseRepository;

class SermonsRepository extends EloquentBaseRepository
{
	public function mostRecent()
    {
        return $this->model->with('series','individual')->orderBy('servicedate', 'DESC')->first();
    }
}
