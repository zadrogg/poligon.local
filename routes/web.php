<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestTestController;
use App\Http\Controllers\Blog\PostController;
use App\Http\Controllers\Blog\Admin\blogPostController;
use App\Http\Controllers\Blog\Admin\CategoryController;
use App\Http\Controllers\DiggingDeeperController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'digging_deeper'], function () {
    Route::get('collections', [DiggingDeeperController::class, 'collections'])
        ->name('digging_deeper.collections');

    Route::get('process-video', [DiggingDeeperController::class, 'processVideo'])
        ->name('digging_deeper.processVideo');

    Route::get('prepare-catalog', [DiggingDeeperController::class, 'prepareCatalog'])
        ->name('digging_deeper.prepareCatalog');
    
});

Route::group(['prefix' => 'blog'], function () {
    Route::resource('posts', PostController::class)->names('blog.posts');
});

//Админка блога
$groupData = [
    'prefix'    => 'admin/blog'
];
Route::group($groupData, function(){
    //BlogCategory
    $methods = ['index','edit', 'update', 'create', 'store'];
    Route::resource('categories', CategoryController::class)
        ->only($methods)
        ->names('blog.admin.categories');
        
    //BlogPost
    Route::resource('posts', blogPostController::class)
        ->except(['show'])
        ->names('blog.admin.posts');
});

//Route::resource('rest',RestTestController::class)->names('restTest');