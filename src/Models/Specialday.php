<?php

namespace Bishopm\Connexion\Models;

use Illuminate\Database\Eloquent\Model;

class Specialday extends Model
{
    protected $guarded = array('id');

    public function household(){
        return $this->belongsTo('Bishopm\Connexion\Models\Household');
    }

}
