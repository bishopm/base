<?php

namespace bishopm\base\Models;

use Illuminate\Database\Eloquent\Model, Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Tags\HasTags;
use Plank\Mediable\Mediable;

class Blog extends Model
{
    use Sluggable;
    use Mediable;
    use HasTags;

    protected $dates = ['deleted_at'];
    protected $guarded = array('id');

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['firstname', 'surname']
            ]
        ];
    }

    public function individual(){
        return $this->belongsTo('bishopm\base\Models\Individual');
    }

}
