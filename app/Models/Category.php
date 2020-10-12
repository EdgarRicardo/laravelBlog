<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";

    protected $fillable = [
        'name',
    ];

    //Relación de uno a muchos - posts relacionados a la categoria
    public function posts(){
        return $this->hasMany('App\Models\Post');
    }
}
