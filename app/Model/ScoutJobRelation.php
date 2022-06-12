<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ScoutJobRelation extends Model
{
    public function scout()
    {
        return $this->belongsTo('App\Model\Scout');
    }

    public function job()
    {
        return $this->belongsTo('App\Model\Job');
    }

}
