<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function ApplyRecord()
    {
        return $this->belongsTo('App\ApplyRecord');
    }
}
