<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\Scout;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use Notifiable;
    use Searchable;

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

    public function recievedScouts()
    {
        return $this->hasMany(Scout::class, 'reciever_id', 'id');
    }
    public function sentScouts()
    {
        return $this->hasMany(Scout::class, 'sender_id', 'id');
    }

    public function searchableAs()
    {
        return 'users';
    }


        /**
     * パスワードリセット通知の送信をオーバーライド
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
      $this->notify(new PasswordResetNotification($token));
    }
}
