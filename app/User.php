<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $fillable = ['email', 'password'];

    protected $hidden   = ['password', 'remember_token'];

    protected $casts    = ['email_verified_at' => 'datetime',];

    public function profile()
    {
        return $this->hasOne('App\Model\Profile');
    }

}
