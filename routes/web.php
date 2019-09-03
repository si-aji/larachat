<?php

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

Route::get('/profile', 'UserController@index')->name('profile');
Route::put('/profile/{id}', 'UserController@update')->name('profile.update');

Route::get('/chat', 'ChatController@index')->name('chat');
Route::post('/sent', 'MessageController@sentMessage')->name('sent_message');
Route::post('/fetch', 'MessageController@fetchMessage')->name('fetch_message');
Route::post('/seen', 'MessageController@seenMessage')->name('seen_message');
Route::delete('/delete/{id}', 'MessageController@deleteMessage')->name('delete_message');
