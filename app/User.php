<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','user_type','company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    Public function profile()
    {
      // Profileモデルのデータを引っ張てくる
      return $this->hasOne('App\Model\Profile');
    }

    public function likes()
    {   
        return $this->hasMany('App\Model\Like');
    }
    public function applyRecords()
    {   
        return $this->hasMany('App\Model\ApplyRecord');
    }
    public function company()
    {   
        return $this->belongsTo('App\Model\Company');
    }
}
