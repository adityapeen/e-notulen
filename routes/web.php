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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/group', [App\Http\Controllers\Admin\MGroupController::class, 'index'])->name('group');
Route::get('/docs', [App\Http\Controllers\GDocsController::class, 'createDocumentFromTemplate'])->name('tes_docs');
Route::get('/check-in/{id}', [App\Http\Controllers\MeetingController::class, 'check_in'])->name('check_in');
Route::post('/join/{id}', [App\Http\Controllers\MeetingController::class, 'join_meeting'])->name('join_meeting');

Route::group(['middleware' => 'admin', "prefix" => "admin", "as" => "admin."], function () {
    Route::resource('/groups', App\Http\Controllers\Admin\MGroupController::class)->except(['show']);
    Route::resource('/users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::resource('/agendas', App\Http\Controllers\Admin\AgendaController::class)->except(['show']);
    Route::resource('/notes', App\Http\Controllers\Admin\NoteController::class)->except(['show']);
    Route::resource('/evidences', App\Http\Controllers\Admin\EvidenceController::class)->except(['show']);
    Route::post('/notes/lock/{id}', [App\Http\Controllers\Admin\NoteController::class, 'lock'])->name('lock');
    Route::get('/agenda', [App\Http\Controllers\Admin\NoteController::class, 'groupByAgenda'])->name('agenda');
    Route::get('/notes/send-mom/{id}', [App\Http\Controllers\MoMController::class, 'send_individual_mom'])->name('notes.mom');
    Route::get('/notes/attendance/{id}', [App\Http\Controllers\MoMController::class, 'mom_recipient'])->name('notes.attendant');
    Route::get('/notes/view/{id}', [App\Http\Controllers\Admin\NoteController::class, 'show'])->name('notes.view');
    Route::get('/notes/test-mom/', [App\Http\Controllers\MoMController::class, 'test_file'])->name('notes.test');
    Route::get('/notes/action/{id}', [App\Http\Controllers\Admin\NoteController::class, 'action_item'])->name('notes.action');
    Route::post('/notes/action/{id}', [App\Http\Controllers\ActionItemsController::class, 'change_status'])->name('notes.action.status');
    Route::get('/notes/agenda/{id}', [App\Http\Controllers\Admin\NoteController::class, 'byAgenda'])->name('notes.agenda');
    Route::get('/notes/show/{id}', [App\Http\Controllers\Admin\NoteController::class, 'showNote'])->name('notes.show');
    Route::get('/notes/action/{id}/evidences', [App\Http\Controllers\Admin\NoteController::class, 'evidence'])->name('notes.evidence');
    Route::get('/notes/action/{id}/evidences/add', [App\Http\Controllers\Admin\EvidenceController::class, 'add'])->name('notes.evidence.add');
    Route::get('/notes/qr/{id}', [App\Http\Controllers\Admin\NoteController::class, 'qrcode'])->name('notes.qrcode');
    Route::get('/notes/export/{id}', [App\Http\Controllers\GDocsController::class, 'exportPDF'])->name('export.docs');
    Route::get('/reminder', [App\Http\Controllers\MoMController::class, 'send_reminder'])->name('notes.reminder');
    Route::get('/users/password', [App\Http\Controllers\Admin\UserController::class, 'password'])->name('users.password');
    Route::post('/users/password', [App\Http\Controllers\Admin\UserController::class, 'change_password'])->name('users.change_password');
});

Route::group(['middleware' => 'user', "prefix" => "user", "as" => "user."], function () {
    Route::resource('/notes', App\Http\Controllers\UserNoteController::class)->except(['show']);
    Route::get('/notes/view/{id}', [App\Http\Controllers\UserNoteController::class, 'show'])->name('notes.show');
    Route::get('/profile', [App\Http\Controllers\Admin\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Admin\UserController::class, 'self_update'])->name('profile.update');
    Route::get('/password', [App\Http\Controllers\Admin\UserController::class, 'password'])->name('password');
    Route::post('/password', [App\Http\Controllers\Admin\UserController::class, 'change_password'])->name('change_password');
});

Route::group(['middleware' => 'api', "prefix" => "api", "as" => "api."], function () {
    Route::get('/attendants/{id}', [App\Http\Controllers\ApiController::class, 'attendants'])->name('attendants'); 
    Route::get('/g_attendants/{id}', [App\Http\Controllers\ApiController::class, 'group_attendants'])->name('group_attendants'); 
    Route::get('/act_pic/{id}', [App\Http\Controllers\ApiController::class, 'action_pic'])->name('action_pic'); 
    Route::get('/all_pic/{id}', [App\Http\Controllers\ApiController::class, 'all_pic'])->name('all_pic');
    Route::get('/notes/{id}', [App\Http\Controllers\ApiController::class, 'note_detail'])->name('notes');
    Route::get('/docs/{id}', [App\Http\Controllers\GDocsController::class, 'createNoteDocs'])->name('gdocs');
    Route::resource('/actions', App\Http\Controllers\ActionItemsController::class)->except(['index']);
});