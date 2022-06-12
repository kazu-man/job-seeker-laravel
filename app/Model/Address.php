<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function job()
    {
        return $this->belongsTo('App\Model\Job');
    }

}
