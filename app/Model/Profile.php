<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id','dob','first_name','last_name','city','state','phone_number', 'address', 'country','zip_code','budget'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
