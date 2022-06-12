<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function TagType()
    {
        return $this->belongsTo('App\Model\TagType');
    }
    public function JobTagRelation()
    {
        return $this->hasMany('App\Model\JobTagRelation');
    }
}
