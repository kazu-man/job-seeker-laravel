<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobTagRelation extends Model
{
    public function Job()
    {
        return $this->belongsTo('App\Model\Job');
    }
    public function Tag()
    {
        return $this->belongsTo('App\Model\Tag');
    }
}
