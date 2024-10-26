<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HomePageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|

| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function(){
    Route::get('/login','loginPage')->name('loginPage');
    Route::get('/signup','signupPage')->name('signupPage');
    Route::post('/login','login')->name('login');
    Route::post('/signup','signup')->name('signup');
});

Route::redirect('/', '/home');

Route::middleware(['auth.check'])->group(function(){
    Route::controller(HomePageController::class)->group(function(){
        Route::get('/home','homePage')->name('home');
    });

    Route::post('/logout',function(){
        Auth::logout();
        return redirect('/login');
    });

    Route::group(['prefix' => 'projects','controller' => ProjectController::class,'as' => 'projects.'], function(){
        Route::get('/','index')->name('get');
        Route::post('/','store')->name('create');
        Route::post('/update','update')->name('update');
        Route::delete('/{id}','destroy')->name('delete');
        Route::post('/set-active/{id}','setActive')->name('set-active');
    });

    Route::group(['prefix' => '/groups','controller' => GroupController::class, 'as' => 'groups.'],function(){
        Route::get('/','index')->name('get');
        Route::post('/','store')->name('create');
        Route::delete('/{groupId}','destroy')->name('delete');
    });

    Route::group(['prefix' => '/apis','controller' => ApiController::class, 'as' => 'apis.'],function(){
        Route::post('/','store')->name('create');
        Route::delete('/{apiId}','destroy')->name('delete');
    });

    Route::group(['prefix' => '/docs','controller' => DocsController::class,'as' => 'docs.'],function(){
        Route::get('/{apiId}','createPage')->name('createPage');
        Route::post('/store','store')->name('create');
        Route::get('/edit/{docId}','edit')->name('edit');
        Route::post('/update','update')->name('update');
    });

});
