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
Route::get('/quick_register/{id}', [App\Http\Controllers\MeetingController::class, 'custom_register'])->name('quick_register');
Route::get('/mom_status/{id}/{type}', [App\Http\Controllers\MoMController::class, 'update_mom_status'])->name('mom_status');
Route::get('/check_api_wa', [App\Http\Controllers\ApiController::class, 'check_api_wa'])->name('check_api_wa');

Route::middleware(['auth'])->group(function () {
    Route::get('/switch-role/{role}', [App\Http\Controllers\SwitchRoleController::class, '__invoke'])->name('switch.role');
    Route::post('/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markNotification'])->name('mark.notif');
});

Route::group(['middleware' => 'admin', "prefix" => "admin", "as" => "admin."], function () {
    Route::resource('/groups', App\Http\Controllers\Admin\MGroupController::class)->except(['show']);
    Route::resource('/users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::resource('/agendas', App\Http\Controllers\Admin\AgendaController::class)->except(['show']);
    Route::resource('/notes', App\Http\Controllers\Admin\NoteController::class)->except(['show']);
    Route::resource('/evidences', App\Http\Controllers\Admin\EvidenceController::class)->except(['show']);
    Route::resource('/teams', App\Http\Controllers\Admin\TeamController::class)->except(['show']);
    Route::resource('/levels', App\Http\Controllers\MLevelController::class)->except(['create','show']);
    Route::post('/notes/lock/{id}', [App\Http\Controllers\Admin\NoteController::class, 'lock'])->name('lock');
    Route::post('/notes/action/{id}', [App\Http\Controllers\ActionItemsController::class, 'change_status'])->name('notes.action.status');
    Route::get('/notes/satker/{id}', [App\Http\Controllers\Admin\NoteController::class, 'bySatker'])->name('notes.satker');
    Route::get('/agenda', [App\Http\Controllers\Admin\NoteController::class, 'groupByAgenda'])->name('agenda');
    Route::get('/agenda/{id}/summary', [App\Http\Controllers\Admin\AgendaController::class, 'edit_summary'])->name('agenda.summary');
    Route::post('/agenda/{id}/summary', [App\Http\Controllers\Admin\AgendaController::class, 'save_summary'])->name('agenda.summary.save');
    Route::get('/notes/send-mom/{id}/{type}', [App\Http\Controllers\MoMController::class, 'send_individual_mom'])->name('notes.mom');
    Route::get('/notes/attendance/{id}', [App\Http\Controllers\MoMController::class, 'mom_recipient'])->name('notes.attendant');
    Route::get('/notes/view/{id}', [App\Http\Controllers\Admin\NoteController::class, 'show'])->name('notes.view');
    Route::get('/notes/test-mom/', [App\Http\Controllers\MoMController::class, 'test_file'])->name('notes.test');
    Route::get('/notes/action/{id}', [App\Http\Controllers\Admin\NoteController::class, 'action_item'])->name('notes.action');
    Route::get('/notes/agenda/{id}', [App\Http\Controllers\Admin\NoteController::class, 'byAgenda'])->name('notes.agenda');
    Route::get('/notes/show/{id}', [App\Http\Controllers\Admin\NoteController::class, 'showNote'])->name('notes.show');
    Route::get('/notes/action/{id}/evidences', [App\Http\Controllers\Admin\NoteController::class, 'evidence'])->name('notes.evidence');
    Route::get('/notes/action/{id}/evidences/add', [App\Http\Controllers\Admin\EvidenceController::class, 'add'])->name('notes.evidence.add');
    Route::get('/notes/qr/{id}', [App\Http\Controllers\Admin\NoteController::class, 'qrcode'])->name('notes.qrcode');
    Route::get('/notes/export/{id}', [App\Http\Controllers\GDocsController::class, 'exportPDF'])->name('export.docs');
    Route::get('/notes/absensi/{id}', [App\Http\Controllers\PDFController::class, 'generateAttendanceList'])->name('notes.absensi');
    Route::get('/action-items/{id}', [App\Http\Controllers\ActionItemsListController::class, 'index_admin'])->name('action_items');
    Route::get('/reminder', [App\Http\Controllers\MoMController::class, 'send_reminder'])->name('notes.reminder');
    Route::get('/users/password', [App\Http\Controllers\Admin\UserController::class, 'password'])->name('users.password');
    Route::post('/users/password', [App\Http\Controllers\Admin\UserController::class, 'change_password'])->name('users.change_password');
    Route::get('/wa-blast', [App\Http\Controllers\Admin\WABlastController::class, 'index'])->name('wa-blast.form');
    Route::post('/wa-blast/send', [App\Http\Controllers\Admin\WABlastController::class, 'send_blast'])->name('wa-blast.send');
    Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/detail/{id}', [App\Http\Controllers\Admin\PerformanceController::class, 'employee'])->name('performance.detail');
    Route::get('/notes/pic/{id}/done', [App\Http\Controllers\Admin\EvidenceController::class, 'change_pic_status'])->name('notes.pic.done');
});

Route::group(['middleware' => 'ses', "prefix" => "ses", "as" => "ses."], function () {
    Route::get('/dashboard', [App\Http\Controllers\Observer\SesController::class, 'index'])->name('dashboard');
    Route::get('/agenda', [App\Http\Controllers\Observer\SesController::class, 'agenda'])->name('agenda');
    // Route::get('/notes', [App\Http\Controllers\Observer\SesController::class, 'notes'])->name('notes');
    Route::get('/notes/satker/{id}', [App\Http\Controllers\Observer\SesController::class, 'notes'])->name('notes');
    Route::get('/notes/agenda/{id}', [App\Http\Controllers\Observer\SesController::class, 'byAgenda'])->name('notes.agenda');
    Route::get('/action-items', [App\Http\Controllers\Observer\SesController::class, 'index'])->name('action_items');
    Route::get('/notes/view/{id}', [App\Http\Controllers\Observer\SesController::class, 'show'])->name('notes.view');
    Route::get('/notes/action/{id}/evidences', [App\Http\Controllers\Observer\SesController::class, 'evidence'])->name('notes.evidence');
    Route::get('/notes/action/{id}', [App\Http\Controllers\Observer\SesController::class, 'action_item'])->name('notes.action');
    Route::get('/notes/absensi/{id}', [App\Http\Controllers\PDFController::class, 'generateAttendanceList'])->name('notes.absensi');
    Route::get('/notes/show/{id}', [App\Http\Controllers\Observer\SesController::class, 'showNote'])->name('notes.show');
    Route::get('/action-items/{id}', [App\Http\Controllers\ActionItemsListController::class, 'index_ses'])->name('action_items');

});

Route::group(['middleware' => 'satker', "prefix" => "satker", "as" => "satker."], function () {
    Route::resource('/agendas', App\Http\Controllers\AdminSatker\SatkerAgendaController::class)->except(['show']);
    Route::resource('/groups', App\Http\Controllers\AdminSatker\SatkerMGroupController::class)->except(['show']);
    Route::resource('/teams', App\Http\Controllers\AdminSatker\SatkerTeamController::class)->except(['show']);
    Route::resource('/users', App\Http\Controllers\AdminSatker\SatkerUserController::class)->except(['create','store','show']);
    Route::resource('/notes', App\Http\Controllers\AdminSatker\SatkerNoteController::class)->except(['show']);
    Route::resource('/evidences', App\Http\Controllers\AdminSatker\SatkerEvidenceController::class)->except(['show']);
    Route::post('/notes/lock/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'lock'])->name('lock');
    Route::post('/notes/action/{id}', [App\Http\Controllers\ActionItemsController::class, 'change_status'])->name('notes.action.status');
    Route::get('/notes/show/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'showNote'])->name('notes.show');
    Route::get('/notes/qr/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'qrcode'])->name('notes.qrcode');
    Route::get('/notes/export/{id}', [App\Http\Controllers\GDocsController::class, 'exportPDF'])->name('export.docs');
    Route::get('/notes/absensi/{id}', [App\Http\Controllers\PDFController::class, 'generateAttendanceList'])->name('notes.absensi');
    Route::get('/notes/send-mom/{id}/{type}', [App\Http\Controllers\MoMController::class, 'send_individual_mom'])->name('notes.mom');
    Route::get('/notes/attendance/{id}', [App\Http\Controllers\MoMController::class, 'mom_recipient'])->name('notes.attendant');
    Route::get('/notes/action/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'action_item'])->name('notes.action');
    Route::get('/notes/action/{id}/evidences', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'evidence'])->name('notes.evidence');
    Route::get('/notes/action/{id}/evidences/add', [App\Http\Controllers\AdminSatker\SatkerEvidenceController::class, 'add'])->name('notes.evidence.add');
    Route::get('/notes/agenda/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'byAgenda'])->name('notes.agenda');
    Route::get('/notes/view/{id}', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'show'])->name('notes.view');
    Route::get('/action-items', [App\Http\Controllers\ActionItemsListController::class, 'index_satker'])->name('action_items');
    Route::get('/agenda', [App\Http\Controllers\AdminSatker\SatkerNoteController::class, 'groupByAgenda'])->name('agenda');
});

Route::group(['middleware' => 'user', "prefix" => "user", "as" => "user."], function () {
    Route::resource('/notes', App\Http\Controllers\User\UserNoteController::class)->except(['show']);
    Route::resource('/evidences', App\Http\Controllers\User\UserEvidenceController::class)->except(['show']);
    Route::resource('/action-items', App\Http\Controllers\User\UserActionItemsController::class)->only(['index']);
    Route::get('/notes/action/{id}', [App\Http\Controllers\User\UserNoteController::class, 'action_items'])->name('notes.action');
    Route::get('/notes/action/{id}/evidences', [App\Http\Controllers\User\UserNoteController::class, 'evidence'])->name('notes.evidence');
    Route::get('/notes/action/{id}/evidences/add', [App\Http\Controllers\User\UserEvidenceController::class, 'add'])->name('notes.evidence.add');
    Route::get('/notes/view/{id}', [App\Http\Controllers\User\UserNoteController::class, 'show'])->name('notes.show');
    Route::get('/profile', [App\Http\Controllers\Admin\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Admin\UserController::class, 'self_update'])->name('profile.update');
    Route::get('/password', [App\Http\Controllers\Admin\UserController::class, 'password'])->name('password');
    Route::post('/password', [App\Http\Controllers\Admin\UserController::class, 'change_password'])->name('change_password');
});

Route::group(['middleware' => 'auth', "prefix" => "api", "as" => "api."], function () {
    Route::get('/attendants/{id}', [App\Http\Controllers\ApiController::class, 'attendants'])->name('attendants'); 
    Route::get('/g_attendants/{id}', [App\Http\Controllers\ApiController::class, 'group_attendants'])->name('group_attendants'); 
    Route::get('/act_pic/{id}', [App\Http\Controllers\ApiController::class, 'action_pic'])->name('action_pic'); 
    Route::get('/all_pic/{id}', [App\Http\Controllers\ApiController::class, 'all_pic'])->name('all_pic');
    Route::get('/notes/{id}', [App\Http\Controllers\ApiController::class, 'note_detail'])->name('notes');
    Route::get('/docs/{id}', [App\Http\Controllers\GDocsController::class, 'createNoteDocs'])->name('gdocs');
    Route::resource('/actions', App\Http\Controllers\ActionItemsController::class)->except(['index']);
    Route::get('/action_detail/{id}', [App\Http\Controllers\ApiController::class, 'action_item_detail'])->name('action_detail');
    Route::get('/comments/{id}', [App\Http\Controllers\CommentController::class, 'get_comments'])->name('get_comments');
    Route::post('/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comment.store');
});