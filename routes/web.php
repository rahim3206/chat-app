<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/send-message', [App\Http\Controllers\HomeController::class, 'send_message'])->name('send-message');
Route::get('/load_chats', [App\Http\Controllers\HomeController::class, 'load_chats'])->name('load_chats');
Route::post('/delete-chats', [App\Http\Controllers\HomeController::class, 'delete_chat'])->name('delete-chat');
Route::post('/update-message', [App\Http\Controllers\HomeController::class, 'update_chat'])->name('update-message');


// Group Chat Route
Route::get('groups',[GroupController::class,'index'])->name('groups');
Route::post('create-group',[GroupController::class,'store'])->name('create-group');
Route::get('search-members',[GroupController::class,'search_member'])->name('search-members');
Route::post('add-member',[GroupController::class,'add_member'])->name('add-member');
Route::get('load_group_chats',[GroupController::class,'load_group_chats'])->name('load_group_chats');
Route::get('members',[GroupController::class,'members'])->name('members');
Route::post('leave_group',[GroupController::class,'leave_group'])->name('leave_group');
Route::post('kick_member',[GroupController::class,'kick_member'])->name('kick_member');
Route::get('search-user',[HomeController::class,'search_user'])->name('search-user');
Route::post('send-request',[HomeController::class,'send_request'])->name('send-request');

Route::get('accept-friend-request',[HomeController::class,'accept_request'])->name('accept.friend.request');
Route::get('cancel-friend-request',[HomeController::class,'cancel_request'])->name('cancel.friend.request');

