<?php

namespace bishopm\base\Models;

use Illuminate\Database\Eloquent\Model;

class Specialday extends Model
{
    protected $guarded = array('id');

    public function household(){
        return $this->belongsTo('bishopm\base\Models\Household');
    }

}
