<?php

namespace bishopm\base\Models;

use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{

    protected $guarded = array('id');


    public function series(){
        return $this->belongsTo('bishopm\base\Models\Series');
    }
    /*
    public function person(){
        return $this->belongsTo('Modules\Website\Entities\Person');
    }*/

}
