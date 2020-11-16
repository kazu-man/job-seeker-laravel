<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    public function provinces()
    {   
        return $this->hasMany('App\Model\Province');
    }
}
