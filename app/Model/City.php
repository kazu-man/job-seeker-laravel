<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    public function province()
    {
        return $this->belongsTo('App\Model\Province');
    }
}
