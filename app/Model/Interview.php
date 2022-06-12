<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    public function applyRecord()
    {
        return $this->belongsTo('App\Model\ApplyRecord');
    }

}
