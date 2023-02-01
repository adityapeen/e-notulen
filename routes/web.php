<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/group', [App\Http\Controllers\Admin\MGroupController::class, 'index'])->name('group');

Route::group(['middleware' => 'admin', "prefix" => "admin", "as" => "admin."], function () {
    Route::resource('/groups', App\Http\Controllers\Admin\MGroupController::class)->except(['show']);
    Route::resource('/users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
});