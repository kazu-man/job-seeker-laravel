<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    Public function category()
    {
      return $this->belongsTo('App\Category');
    }

    Public function profile()
    {
      return $this->belongsTo('App\Profile');
    }

}
