<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = ['company_name','company_image'];


    public function applyRecords()
    {   
        return $this->hasMany('App\Model\ApplyRecord');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
