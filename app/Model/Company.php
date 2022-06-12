<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Company extends Model
{
    use Searchable;

    protected $fillable = ['company_name','company_image'];


    public function applyRecords()
    {   
        return $this->hasMany('App\Model\ApplyRecord');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function jobs()
    {   
        return $this->hasMany('App\Model\Job');
    }
}
