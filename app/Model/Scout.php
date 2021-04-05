<?php

namespace App\Model;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Scout extends Model
{
    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function reciever()
    {
        return $this->hasOne(User::class, 'id', 'reciever_id');
    }

    public function jobRelations()
    {
        // return $this->hasOne(ScoutJobRelations::class, 'scout_id', 'id');

        return $this->hasMany('App\Model\ScoutJobRelation');
    }
}
