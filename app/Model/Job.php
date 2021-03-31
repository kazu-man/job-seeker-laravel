<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    public function jobDescription()
    {
        return $this->belongsTo('App\Model\JobDescription');
    }
    public function company()
    {
        return $this->belongsTo('App\Model\Company');
    }
    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }
    public function city()
    {
        return $this->belongsTo('App\Model\City');
    }

    public function jobType()
    {
        return $this->belongsTo('App\Model\JobType');
    }

    public function likes()
    {
        return $this->hasMany('App\Model\Like');
    }
    public function applyRecords()
    {   
        return $this->hasMany('App\Model\ApplyRecord');
    }
    public function JobTagRelations()
    {
        return $this->hasMany('App\Model\JobTagRelation');
    }



}
