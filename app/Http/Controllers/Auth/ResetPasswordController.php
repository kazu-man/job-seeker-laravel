<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\User;
use App\Model\ResetPassword;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    // vueでアクセスするログインへのルート
    protected $vueRouteLogin = 'login';
    // vueでアクセスするリセットへのルート
    protected $vueRouteReset = 'reset';
    // server\config\auth.phpで設定していない場合のデフォルト
    protected $expires = 600 * 5;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // guestミドルウェアはRedirectIfAuthenticatedクラスを指定しているので
        // 認証済み（ログイン済み）の状態でログインページにアクセスすると、ログイン後のトップページにリダイレクトする
        $this->middleware('guest');

        // server\config\auth.phpで設定した値を取得、ない場合はもとの値
        $this->expires = config('auth.reset_password_expires', $this->expires);
    }

    /**
     * reset password
     * パスワード変更メールからのコールバック
     *
     * @param string $token
     * @return Redirect
     */
    public function resetPassword($token = null)
    {
        // トークンがあるかチェック
        $isNotFoundResetPassword = ResetPassword::where('token', $token)
            ->doesntExist();

        // なかったとき
        if ($isNotFoundResetPassword) {
            // メッセージをクッキーにつけてリダイレクト
            $message = Lang::get('password reset email has not been sent.');
            return $this->redirectWithMessage($this->vueRouteLogin, $message);
        }

        // トークンをクッキーにつけてリセットページにリダイレクト
        return $this->redirectWithToken($this->vueRouteReset, $token);
    }

    /**
     * reset
     * パスワードリセットApi
     *
     * @param Request $request
     * @return void
     */
    public function reset(Request $request)
    {
        // バリデーション
        $validator = $this->validator($request->all());
        $validator->validate();

        $resetPassword = ResetPassword::where('email', $request->email)->first();

        // 送られてきたトトークン
        $token = $request->token;

        // $token = $request->token;
        if (!Hash::check($token, $resetPassword->token)) {
            return response()->json([
                'message' => 'tokenが一致しません',
            ], 503);
        }

        // リセットパスワードモデルを取得

        // ユーザの宣言
        $user = null;

        // 追加のバリデーション
        $validator->after(function ($validator) use ($resetPassword, &$user) {

            // リセットパスワードがない場合
            if (!$resetPassword) {
                $validator->errors()->add('token', __('invalid token.'));
            }

            // トークン期限切れチェック
            $isExpired = $this->tokenExpired($resetPassword->created_at);
            if ($isExpired) {

                $validator->errors()->add('token', __('Expired token.'));
            }

            // ユーザの取得
            $user = User::where('email', $resetPassword->email)->first();

            // ユーザの存在チェック
            if (!$user) {

                $validator->errors()->add('token', __('is not user.'));
            }
        });

        // これで、バリデーションがある場合に、jsonレスポンスを返す
        $validator->validate();

        // トランザクション、アップデート後のユーザを返す
        $user = DB::transaction(function () use ($request, $resetPassword, $user) {

            // リセットパスワードテーブルからデータを削除
            ResetPassword::destroy($resetPassword->email);

            // パスワードを変更
            $user->password = Hash::make($request->password);

            // リメンバートークンを変更
            $user->setRememberToken(Str::random(60));

            // データを保存
            $user->save();

            // ユーザを返却
            return $user;
        });

        // イベントを発行
        event(new PasswordReset($user));

        // ユーザをログインさせる
        Auth::login($user, true);

        // ユーザを返却
        return $user;
    }

    /**
     * validator
     *
     * @param  array  $data
     * @return \Illuminate\Support\Facades\Validator;
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // 'email' => ['email'],
            'email' => [function($attribute, $value, $fail){
                if($value == null || $value == ""){
                    return $fail('メールアドレスを入力してください');
                }
                $resetUser = ResetPassword::where('email', $value)->first();
                \Log::info($resetUser);
                if($resetUser == null){
                    return $fail('メールアドレスからユーザが見つかりませんでした');
                }
            },
            ],
            'token' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * redirect with message
     *
     * @param  string  $route
     * @param  string  $message
     * @return Redirect
     */
    protected function redirectWithMessage($vueRoute, $message)
    {
        // vueでアクセスするルートを作る
        // コールバックURLをルート名で取得
        // TODO: これだとホットリロードでポートがとれない
        // $route = url($vueRoute);

        // TODO: とりあえずこれで対応
        // .envの「APP_URL」に設定したurlを取得
        $baseUrl = config('app.url');
        $route = "{$baseUrl}/{$vueRoute}";

        return redirect($route)
            // PHPネイティブのsetcookieメソッドに指定する引数同じ
            // ->cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly)
            ->cookie('MESSAGE', $message, 0, '', '', false, false);
    }

    /**
     * redirect with token
     *
     * @param  string  $route
     * @param  string  $message
     * @return Redirect
     */
    protected function redirectWithToken($vueRoute, $token)
    {
        // vueでアクセスするルートを作る
        // コールバックURLをルート名で取得
        // TODO: これだとホットリロードでポートがとれない
        // $route = url($vueRoute);

        // TODO: とりあえずこれで対応
        // .envの「APP_URL」に設定したurlを取得
        $baseUrl = config('app.url');
        $route = "{$baseUrl}/{$vueRoute}";
        return redirect($route)->cookie('RESETTOKEN', $token, 0, '', '', false, false);
    }


    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkUrlBeforeChangePassword(Request $request, $token = null)
    {
        \Log::info($token);
        if($token == null){
            return response()->json([
                'message' => 'tokenが不正です',
            ], 503);
        }
        $resetPasswords = ResetPassword::all();
        \Log::info($resetPasswords);
        $resetTargetFlg = false;
        foreach($resetPasswords as $reset){
            if (Hash::check($token, $reset->token)) {
                $resetTargetFlg = true;
            }
        }
        if(!$resetTargetFlg){
            return response()->json([
                'message' => 'tokenが不正です',
            ], 503);
        }

        // return view('auth.passwords.reset')->with(
        //     ['token' => $token, 'email' => $request->email]
        // );
    }
}