<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name','username','phone_number', 'email', 'password',
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

    public function reciever()
    {
        return $this->hasOne(\App\FriendRequest::class, 'sender_id')->where(['reciever_id' => auth()->id()]);
    }

    public function sender()
    {
        return $this->hasOne(\App\FriendRequest::class, 'reciever_id')->where(['sender_id' => auth()->id()]);
    }

    public function friendRequest()
    {
        return $this->sender->merge($this->reciever);
    }

    public function getIsRequestSentAttribute()
    {

    }
}
