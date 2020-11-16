<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    //

    public function cities()
    {
        return $this->hasMany('App\Model\City');
    }

    public function country()
    {
        return $this->belongsTo('App\Model\Country');
    }
}
