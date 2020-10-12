<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;

class User extends AuthUser
{
    use Notifiable;
    protected $table = "users";

    protected $fillable = [
        'name', 'surname','email', 'password', 'image'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    //Relación de uno a muchos - post relacionados al usuario
    public function posts(){
        return $this->hasMany('App\Models\Post');
    }


}
