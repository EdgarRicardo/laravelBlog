<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Test routes
Route::view('/','welcome')->name('menu');
Route::get('/orm','controllerTest@testORM')->name('orm');
Route::get('/holis','controllerTest@index');
Route::get('/crear/test','controllerTest@create');

// User Controller Routes
Route::post('/newUser', 'UserController@store');
Route::post('/login', 'UserController@login');
Route::put('/updateUser', 'UserController@update')->middleware('api.auth');
Route::post('/uploadAvatar', 'UserController@uploadAvatar')->middleware('api.auth');
Route::get('/getAvatar/{filename}', 'UserController@getImageUsers');
Route::get('/userInfo/{id}', 'UserController@userInfo');

//Category Controller Routes
Route::resource('/category', 'CategoryController');

//Post Controller Routes
Route::resource('/post', 'PostController');
Route::post('/uploadImage', 'PostController@uploadImagePost');
Route::get('/getImage/{filename}', 'PostController@getImagePosts');
Route::get('/postsCategory/{id}', 'PostController@postsByCategory');
Route::get('/postsUser/{id}', 'PostController@postsByUser');

