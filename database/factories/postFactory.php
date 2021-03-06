<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' =>  $faker->numberBetween($min = 1, $max = 10),
        'category_id' =>  $faker->numberBetween($min = 1, $max = 5),
        'title' => $faker->lexify('The ??????? ????'),
        'image' => '1602700133unnamed.png',
        'content' => $faker->text($maxNbChars = 1500),
    ];
});
