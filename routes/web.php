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
    Route::resource('/agendas', App\Http\Controllers\Admin\AgendaController::class)->except(['show']);
    Route::resource('/notes', App\Http\Controllers\Admin\NoteController::class)->except(['show']);
    Route::post('/notes/lock/{id}', [App\Http\Controllers\Admin\NoteController::class, 'lock'])->name('lock');
    Route::get('/notes/send-mom/{id}', [App\Http\Controllers\MoMController::class, 'send_mom'])->name('notes.mom');
    Route::get('/notes/view/{id}', [App\Http\Controllers\Admin\NoteController::class, 'show'])->name('notes.view');
});

Route::group(['middleware' => 'api', "prefix" => "api", "as" => "api."], function () {
    Route::get('/attendants/{id}', [App\Http\Controllers\ApiController::class, 'attendants'])->name('attendants'); 
});