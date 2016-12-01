<?php

namespace bishopm\base\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $guarded = array('id');

    public function project()
    {
        return $this->belongsTo('bishopm\base\Models\Project');
    }

    public function individual()
    {
        return $this->belongsTo('bishopm\base\Models\Individual');
    }

    public function folder()
    {
        return $this->belongsTo('bishopm\base\Models\Folder');
    }
}
