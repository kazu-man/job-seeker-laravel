<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function experiences(){

        return $this->hasMany('App\Model\Experience');
        
      }

    protected $table = 'profiles';

}
