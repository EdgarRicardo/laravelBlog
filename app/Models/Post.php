<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

    protected $fillable = [
        'user_id', 'category_id','title', 'content', 'image',
    ];

    //Relación de muchos a uno - posts relacionados a un usuario
    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

    //Relación de muchos a uno - posts relacionados a una categoria
    public function category(){
        return $this->hasMany('App\Models\Categorie','category_id');
    }
}
