<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//入口頁面首頁
Route::get('/', 'HomeController@index')->name('home');

Route::group(['prefix' => 'member'], function () {
    Route::get('/', 'MemberController@getIndex')->name('member.list');
    Route::get('login', 'MemberController@getLogin')->name('member.login');
    Route::post('login', 'MemberController@postLogin')->name('member.login');
    Route::get('register', 'MemberController@getRegister')->name('member.register');
    Route::post('register', 'MemberController@postRegister')->name('member.register');
    Route::get('confirm/{token?}', 'MemberController@getConfirm')->name('member.confirm');
    Route::get('resend', 'MemberController@getResend')->name('member.resend');
    Route::post('resend', 'MemberController@postResend')->name('member.resend');
    Route::get('forgot-password', 'MemberController@getForgotPassword')->name('member.forgot-password');
    Route::post('forgot-password', 'MemberController@postForgotPassword')->name('member.forgot-password');
    Route::get('reset-password/{token?}', 'MemberController@getResetPassword')->name('member.reset-password');
    Route::post('reset-password', 'MemberController@postResetPassword')->name('member.reset-password');
    Route::get('change-password', 'MemberController@getChangePassword')->name('member.change-password');
    Route::post('change-password', 'MemberController@postChangePassword')->name('member.change-password');
    Route::get('profile/{uid?}', 'MemberController@getProfile')->name('member.profile');
    Route::get('edit-profile', 'MemberController@getEditProfile')->name('member.edit-profile');
    Route::post('edit-profile', 'MemberController@postEditProfile')->name('member.edit-profile');
    Route::get('edit-other-profile/{uid}', 'MemberController@getEditOtherProfile')->name('member.edit-other-profile');
    Route::post('edit-other-profile/{uid?}', 'MemberController@postEditOtherProfile')
        ->name('member.edit-other-profile');
    Route::get('logout', 'MemberController@getLogout')->name('member.logout');
});

//投票系統
Route::post('vote-event/start/{vid}', 'VoteEventController@start')->name('vote-event.start');
Route::post('vote-event/end/{vid}', 'VoteEventController@end')->name('vote-event.end');
Route::post('vote-event/sort/{vid}', 'VoteEventController@sort')->name('vote-event.sort');
Route::resource('vote-event', 'VoteEventController');
Route::post('vote-selection/vote/{id}', 'VoteSelectionController@vote')->name('vote-selection.vote');
Route::resource('vote-selection', 'VoteSelectionController', ['except' => ['index', 'show']]);
Route::resource('organizer', 'OrganizerController');

//寄送測試信
Route::post('send-test-mail', 'SettingController@sendTestMail')->name('send-test-mail');

//Queue狀態
Route::get('queue-status', 'QueueStatusController@index')->name('queue-status');

//網站設定
Route::resource('setting', 'SettingController', [
    'except' => [
        'create',
        'store',
        'destroy',
    ],
]);

//統計頁面
Route::group(['prefix' => 'stats'], function () {
    Route::get('/', 'StatsController@getIndex')->name('stats.index');
    Route::get('force-renew', 'StatsController@getForceRenew')->name('stats.force-renew');
});

//Markdown API
Route::any('markdown', 'MarkdownApiController@markdownPreview')->name('markdown.preview');

//上傳
Route::group(['prefix' => 'upload'], function () {
    Route::post('image', 'UploadController@postImage')->name('upload.image');
    Route::post('delete-image', 'UploadController@deleteImage')->name('upload.delete-image');
});

Route::get('policies/{tab?}', 'PolicyController@index')->name('policies');
