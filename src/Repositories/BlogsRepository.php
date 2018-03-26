<?php namespace Bishopm\Connexion\Repositories;

use Bishopm\Connexion\Repositories\EloquentBaseRepository;

class BlogsRepository extends EloquentBaseRepository
{
    public function mostRecent($num=1)
    {
        return $this->model->with('individual')->where('status', 'published')->orderBy('created_at', 'DESC')->get()->take($num);
    }

    public function allPublished()
    {
        return $this->model->with('individual')->where('status', 'published')->orderBy('created_at', 'DESC')->get();
    }

    public function findByDateSlug($yr, $mth, $slug)
    {
        $testdate=$yr . '-' . $mth . '-%';
        return $this->model->where('created_at', 'like', $testdate)->where('slug', $slug)->first();
    }
}
