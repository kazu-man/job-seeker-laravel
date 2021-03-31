<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TagType extends Model
{
    public function tags()
    {   
        return $this->hasMany('App\Model\Tag');
    }
}
