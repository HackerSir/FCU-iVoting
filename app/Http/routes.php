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
Route::get('/', [
    'as' => 'home',
    'uses' => 'HomeController@index'
]);

Route::controller('member', 'MemberController', array(
    'getIndex' => 'member.list',
    'getLogin' => 'member.login',
    'postLogin' => 'member.login',
    'getRegister' => 'member.register',
    'postRegister' => 'member.register',
    'getConfirm' => 'member.confirm',
    'getResend' => 'member.resend',
    'postResend' => 'member.resend',
    'getForgotPassword' => 'member.forgot-password',
    'postForgotPassword' => 'member.forgot-password',
    'getResetPassword' => 'member.reset-password',
    'postResetPassword' => 'member.reset-password',
    'getChangePassword' => 'member.change-password',
    'postChangePassword' => 'member.change-password',
    'getProfile' => 'member.profile',
    'getEditProfile' => 'member.edit-profile',
    'postEditProfile' => 'member.edit-profile',
    'getEditOtherProfile' => 'member.edit-other-profile',
    'postEditOtherProfile' => 'member.edit-other-profile',
    'getLogout' => 'member.logout'
));

//投票系統
Route::post('vote-event/start/{vid}', [
    'as' => 'vote-event.start',
    'uses' => 'VoteEventController@start'
]);
Route::post('vote-event/end/{vid}', [
    'as' => 'vote-event.end',
    'uses' => 'VoteEventController@end'
]);
Route::resource('vote-event', 'VoteEventController');
Route::post('vote-selection/vote/{id}', [
    'as' => 'vote-selection.vote',
    'uses' => 'VoteSelectionController@vote'
]);
Route::resource('vote-selection', 'VoteSelectionController', ['except' => ['index', 'show']]);
Route::resource('organizer', 'OrganizerController');

//網站設定
Route::resource('setting', 'SettingController', ['except' => ['create', 'store', 'destroy']]);

//Markdown API
Route::any('markdown', [
    'as' => 'markdown.preview',
    'uses' => 'MarkdownApiController@markdownPreview'
]);

//上傳
Route::controller('upload', 'UploadController', [
    'postImage' => 'upload.image',
    'deleteImage' => 'upload.delete-image'
]);

//Log Viewer
Route::get('log', [
    'as' => 'log',
    'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index',
    'middleware' => 'admin'
]);

$policiesTabs = ['privacy', 'terms'];
Route::get('policies/{tab}', [
        'as' => 'policies',
        function ($tab) use ($policiesTabs) {
            if (!in_array($tab, $policiesTabs)) {
                return redirect()->route('policies', $policiesTabs[0]);
            }
            return response()->view('policies');
        }]
);

//未定義路由
Route::get('{all}', array(
    'as' => 'not-found',
    function () {
        abort(404);
    }
))->where('all', '.*');
