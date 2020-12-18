<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestTestController;
use App\Http\Controllers\Blog\PostController;


Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'blog'], function () {
    Route::resource('posts', PostController::class)->names('blog.posts');
});

Route::resource('rest',RestTestController::class)->names('restTest');