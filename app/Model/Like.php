<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //

    Public function user()
    {
      return $this->belongsTo('App\User');
    }


    Public function job()
    {
      return $this->belongsTo('App\Model\Job');
    }

}
