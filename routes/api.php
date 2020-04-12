<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/tests', function (Request $request) {

//        $header = $request->header('authorization');
//        [$type, $token]= explode(" ", $header);
        // 根据token 获取 oauth_access_tokens 表的过期时间
        // Laravel\Passport\Token obj
        $check = Auth::user()->token();
        // 过期时间
        $expiresAt = \Carbon\Carbon::parse($check->expires_at)->toDateString();
        var_dump($expiresAt);
        dd($check);
//        return $check->expires_at;
    });



    Route::get('/deletes', function () {
        if (Auth::check()) {
            Auth::user()->token()->delete();
        }


        return 'ok';
    });

    Route::get('/logout', function () {
       $id = Auth::user()->token()->getQueueableId();
       DB::table('oauth_access_tokens')->where('id', $id)->update(['revoked' => 1]);
    });
});

Route::post('/login', 'PassportsController@check');



