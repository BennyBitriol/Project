<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use App\Models\Users;
use App\SocialAccounts;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp;

class LineLoginController extends Controller 
{
    public function redirectToProvider($provider = 'line')
    {
        // return Socialite::driver($provider)->redirect();

        $url =  Socialite::driver($provider)->redirect()->getTargetUrl();
        return response()->json([
            "url" => $url
        ]);
    }

    public function handleProviderCallback($provider = 'line')
    {
       
        $input = request()->all();
        $fcode = $input['code'];

        $client = new GuzzleHttp\Client();
        $res = $client->post('https://api.line.me/oauth2/v2.1/token', [
            'form_params' => [
                'code' => $fcode,
                'redirect_uri' => env('LINE_REDIRECT_URI'),
                'client_id'=> env('LINE_CHANNEL_ID'),
                'client_secret'=> env('LINE_SECRET'),
                'grant_type'=>'authorization_code',
            ]
        ]);
        $xxx = $res->getBody()->getContents();
        $yyy = json_decode($xxx);
        $array = get_object_vars($yyy);
        $access_token = $array['access_token'];
        
        $client2 = new GuzzleHttp\Client();
        $headers = [
            'Authorization' => 'Bearer ' . $access_token,        
        ];            
        $response = $client2->request('GET', 'https://api.line.me/v2/profile', [
            'headers' => $headers
        ]);
        $line_callback = $response->getBody()->getContents();
        $line_callback = json_decode($line_callback);
        $line_callback = get_object_vars($line_callback);
        

        // $providerUser = Socialite::driver($provider)->user();
        $line_id_count = Users::where('line_id',$line_callback['userId'])->count();


        if($line_id_count==0){ /// ยังไม่ได้ลงใน DB
            $data = Users::create([
                    'email' => rand(10,100),
                    'name' => 'BOBO',
                    'email_verified_at' => "2021-01-30 22:28:23",
                    'password' => "bobo",
                    'line_id' => $line_callback['userId'],
                    'line_avatar' => $line_callback['pictureUrl'],
                    'line_name' => $line_callback['displayName'],
                    'remember_token' => 'asdzxc',
                    'created_at' => "2021-01-30 22:28:23" , 
                    'updated_at' => "2021-01-30 22:28:23",
                ]);
        
            $data->save();
            $user = Users::where('line_id',$line_callback['userId'])->first();
            
            /// auth
            
            
            $token = JWTAuth::fromUser($user);
            
            return response()->json(compact('token'),201);
            
            
            
        }else if($line_id_count>0){
            $user = Users::where('line_id',$line_callback['userId'])->first();
             /// auth
            
            $token = JWTAuth::fromUser($user);
            
            return response()->json(compact('token'),201);
            
        }
    }







    
    // public function LineMe(){
    //     try {

    //         if (! $user = JWTAuth::parseToken()->authenticate()) {
    //                 return response()->json(['user_not_found'], 404);
    //         }

    // } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

    //         return response()->json(['token_expired'], $e->getStatusCode());

    // } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

    //         return response()->json(['token_invalid'], $e->getStatusCode());

    // } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

    //         return response()->json(['token_absent'], $e->getStatusCode());

    // }

    // return response()->json(compact('user'));
    // }

    // //test function
    // public function test(){

    //     // Get Account เคยสมัคร line แล้ว

    //     $user = Users::find('line_id',5555);

    //     dd($user);

    //     $data = Users::create([
    //                 'email' => rand (10,100),
    //                 'name' => "benny",
    //                 'email_verified_at' => "2021-01-30 22:28:23",

    //                 'password' => bcrypt(rand(1000, 9999)),
    //                 'remember_token' => "asdzxc",
    //                 'created_at' => "2021-01-30 22:28:23" , 
    //                 'updated_at' => "2021-01-30 22:28:23",
    //             ]);
        
    //     $data->save();

    //     $users =  Users::all();
    //     dd($users);
    // }


    // public function createOrGetUser($provider, $providerUser)
    // {
    //     /** Get Social Account */
    //     $account = SocialAccount::whereProvider($provider)
    //         ->whereProviderUserId($providerUser->getId())
    //         ->first();

    //     if ($account) {
    //         return $account->user;
    //     } else {

    //         /** Get user detail */
    //         $userDetail = Socialite::driver($provider)->userFromToken($providerUser->token);

    //         /** Create new account */
    //         $account = new SocialAccount([
    //             'provider_user_id' => $providerUser->getId(),
    //             'provider' => $provider,
    //         ]);

    //         /** Get email or not */
    //         $email = !empty($providerUser->getEmail()) ? $providerUser->getEmail() : $providerUser->getId() . '@' . $provider . '.com';

    //         /** Get User Auth */
    //         if (auth()->check()) {
    //             $user = auth()->user();
    //         }else{
    //             $user = User::whereEmail($email)->first();
    //         }

    //         if (!$user) {
    //             /** Get Avatar */
    //             $image = $provider . "_" . $providerUser->getId() . ".png";
    //             $imagePath = public_path(config('app.media.directory') . "users/avatar/" . $image);
    //             file_put_contents($imagePath, file_get_contents($providerUser->getAvatar()));


    //             /** Create User */
    //             $user = User::create([
    //                 'email' => $email,
    //                 'name' => $providerUser->getName(),
    //                 'username' => $providerUser->getId(),
    //                 'avatar' => $image,
    //                 'password' => bcrypt(rand(1000, 9999)),
    //             ]);

    //         }

    //         /** Attach User & Social Account */
    //         $account->user()->associate($user);
    //         $account->save();

    //         return $user;
    //     }
    // }
}
