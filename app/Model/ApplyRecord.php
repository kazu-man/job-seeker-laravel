<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ApplyRecord extends Model
{
    //

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function Job()
    {
        return $this->belongsTo('App\Model\Job');
    }
    public function messages()
    {
        return $this->hasMany('App\Model\Message');
    }
    public function interviews()
    {
        return $this->hasMany('App\Model\Interview');
    }


}
