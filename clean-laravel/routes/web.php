<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

use App\Models\Users;

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

use App\Models\sensor_data;

Route::prefix('login')->group(function () {
    Route::get('/{provider}', 'Auth\LineLoginController@redirectToProvider');
    Route::get('/{provider}/callback', 'Auth\LineLoginController@handleProviderCallback');
    Route::get('/{provider}/me', 'Auth\LineLoginController@LineMe');
}); 

Route::get('benny',function(){
    $xxx = sensor_data::where('date','undefined')->whereRaw('sensor_id = 37 or sensor_id = 38')->get();

    foreach ($xxx as $each) {
        $borrow = sensor_data::find(($each->id)-1);

        $stupid = sensor_data::find($each->id);

        $date = $borrow->date;

        $stupid->date = $date;
        $stupid->save();
        
    }
    

    dd($xxx);
});

Route::get('foo/bar', function()
{   
    // $response = Http::post('https://api.line.me/oauth2/v2.1/token', [
    //     'code' => 'F18r1nj4SJrrL6Qxv3VN',
    //     'redirect_uri' => 'http://localhost:8081/login/line/callback',
    //     'client_id'=>'1655594666',
    //     'client_secret'=>'6f040d14c596bc1ea302258fa1007b03',
    //     'grant_type'=>'authorization_code',
    // ]);
    // dd($response);
    
    $res = Http::post('https://api.line.me/oauth2/v2.1/token', [
        'form_params' => [
            'code' => 'F18r1nj4SJrrL6Qxv3VN',
            'redirect_uri' => 'http://localhost:8081/login/line/callback',
            'client_id'=>'1655594666',
            'client_secret'=>'6f040d14c596bc1ea302258fa1007b03',
            'grant_type'=>'authorization_code',
        ]
    ]);
    dd($res);
});
Route::get('/login/line/bypasscallback', function()
{   
    $user = Users::where('id',10)->first();
             /// auth
             
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user','token'),201);
});
Route::get('/lm', function()
{   
    $res = new GuzzleHttp\Client(['base_uri' => 'https://api.line.me/v2/bot/message/']);
    $response = $res->request('POST', 'multicast', [
        'headers' => [
            'Authorization' => 'Bearer ' . 'PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=',        
        ],
        'json' => [
        'to' => ['Uc5ba53e4db1414cb8bb381d2c8a26024', 'U0b20bf22d6f2264ad12132613f81ff5f', 'Uda4196f196c788bd72519e90e7dc7c93'],
        'messages' => [
                [
                    'type' => 'text',
                    'text' => 'Kuy'
                ],
                [
                    'type' => 'text',
                    'text' => 'Ball'
                ]
            ]
        ]
    ]);
    // $res = Http::post('https://api.line.me/v2/bot/message/multicast', [
    
        // 'headers' => [
        //     'Authorization' => 'Bearer ' . "PulgodV3lpjGnJhW1oWqL+msQa4nY4NGYSyIAFCcK+mWYwu2ncVoDkQYRxi/8IJm5RF2V1YLr9Ju6KF/wL28iP0Sjx01R+oacQIF1JLRWVG9jXCJ+UraceHP+EYx5VjIWxx8yYCpvEfsAdOPyKNIfgdB04t89/1O/w1cDnyilFU=",
        //     'Accept' => 'application/json',   
        //     'Content-Type' => 'application/json',
        // ],
        // 'form_params' =>[
        //     'to' => ['Uc5ba53e4db1414cb8bb381d2c8a26024', 'U0b20bf22d6f2264ad12132613f81ff5f', 'Uda4196f196c788bd72519e90e7dc7c93'],
        //     'messages' => [
        //         [
        //             'type' => 'text',
        //             'text' => 'Kuy'
        //         ]
        //         ]
        // ]
        
    // ]);
    return $response;
});