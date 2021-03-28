<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/province', function () {
    $province = Province::find(1)->province_name;
    return 'welcome' . $province;
});


Route::get('/home', 'HomeController@index')->name('home');

//setting
Route::get('/getCountries','SettingController@getCountries');
Route::post('/getProvinces','SettingController@getProvinces');
Route::post('/getCities','SettingController@getCities');
Route::get('/getPlaceData', 'SettingController@getPlaceData');
Route::get('/getJobType', 'SettingController@getJobType');
Route::get('/getInitData', 'SettingController@getInitData');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/deleteCity/{id}', 'SettingController@deleteCity');
    Route::get('/getSettingData','SettingController@getSettingData');
    Route::post('/registerAdmin','SettingController@registerAdmin');
    Route::get('/getPlaceForShowList', 'SettingController@getPlaceForShowList')->name('getPlaceForShowList');
    Route::post('/registerProvince', 'SettingController@registerProvince')->name('registerProvince');
    Route::post('/registerCity', 'SettingController@registerCity')->name('registerCity');
    Route::get('/getTargetProvinces/{id}','SettingController@getTargetProvinces')->name('getTargetProvinces');
    Route::post('/addAdmin', 'SettingController@addAdmin')->name('addAdmin');
    Route::post('/userDelete', 'SettingController@userDelete')->name('userDelete');
    Route::post('/registerCountry', 'SettingController@registerCountry')->name('registerCountry');
    Route::post('/passwordUpdate', 'SettingController@passwordUpdate')->name('passwordUpdate');
});

//post
Route::post('/getPosts','PostController@getPosts')->name('getPosts');

Route::group(['middleware' => ['auth']], function () {
    Route::post('/updatePost', 'PostController@updatePost')->name('updatePost');
    Route::post('/closePost', 'PostController@closePost')->name('closePost');
    Route::get('/getSinglePost/{id}', 'PostController@getSinglePost')->name('getSinglePost');
    Route::post('/registerJob','PostController@registerJob')->name('registerJob');
});

//jobList
Route::group(['middleware' => ['auth']], function () {
    Route::post('/updateProfile', 'JobsListController@updateProfile')->name('updateProfile');
    Route::get('/getProfile', 'JobsListController@getProfile')->name('getProfile');
    Route::get('/getApplicantProfile/{id}', 'JobsListController@getApplicantProfile')->name('applicantProfile');
    
    Route::post('/addLike', 'JobsListController@addLike')->name('addLike');
    Route::get('/getLikeList', 'JobsListController@getLikeList')->name('getLikeList');
    Route::post('/removeLike', 'JobsListController@removeLike')->name('removeLike');
    Route::get('/getApplyList', 'JobsListController@getApplyList')->name('getApplyList');
    
    Route::get('/getSingleApplyRecord/{id}', 'JobsListController@getSingleApplyRecord')->name('getSingleApplyRecord');
    Route::post('/apply', 'JobsListController@apply')->name('apply');
    Route::get('/getApplyRecords', 'JobsListController@getApplyRecords')->name('applyRecords');
    
    Route::get('/getMessages/{id}', 'JobsListController@getMessages')->name('getMessages');
    Route::post('/sendMessage', 'JobsListController@sendMessage')->name('sendMessage');
    Route::get('/getNewMessageExistFlg', 'JobsListController@getNewMessageExistFlg')->name('getNewMessageExistFlg');
    Route::post('/resumeDownload',  'JobsListController@resumeDownload');    
});

//google login
Route::get('/auth/{service}', 'OAuthLoginController@getGoogleAuth')->where('service', 'google');
Route::get('/auth/callback/google', 'OAuthLoginController@authGoogleCallback');



//auth
Auth::routes();
Route::get('/checkUrlBeforeChangePassword/{token}', 'Auth\ResetPasswordController@checkUrlBeforeChangePassword')->name('getNewcheckUrlBeforeChangePasswordMessageExistFlg');

Route::get('/user', function(){ 
    return Auth::user();
})->name('user');



Route::get('/category', 'SettingController@getCategories');
Route::post('/category', 'SettingController@storeCategory');
Route::post('/category/delete','SettingController@deleteCategory');


