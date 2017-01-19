<?php

namespace bishopm\base\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Preacher extends Model
{

	use Sluggable;

    protected $guarded = array('id');

	public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['firstname', 'surname']
            ]
        ];
    }

    public function society(){
        return $this->belongsTo('bishopm\base\Models\Society');
    }
    

}