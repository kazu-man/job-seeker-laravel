<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Socialite;
use Auth;

class OAuthLoginController extends Controller
{
    // Googleの認証ページへのリダイレクト処理
    public function getGoogleAuth($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Googleの認証情報からユーザー情報の取得
    public function authGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::firstOrNew(['email' => $googleUser->email]);

        if (!$user->exists) {

            $userName = $googleUser->getName();
            $user['name'] = $userName;

            $firstName = explode(" ",$userName)[0];
            $user['user_firstname'] = $firstName;

            if(strlen($userName) > 1){
                $lastName = explode(" ",$userName)[1];
                $user['user_lastname'] = $lastName;
            } 

            $user['email'] = $googleUser->email; // Gmailアドレス
            $user['password'] = str_random(); 

            $user->save();
        }

        Auth::login($user);
        return redirect(config('app.APP_URL')."top");
    }
}