<?php

use App\Model\User;
use App\Model\Province;
use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth'], function(){

    Route::post('auth/video_chat', 'VideoChatController@auth'); // 認証ページ

});
Route::get('/{any?}', 'JobsListController@index')->where('any', '.+');


