<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    // ✅ ログインフォームを表示するメソッド
    public function showLoginForm()
    {
        return view('auth.user_login'); // resources/views/auth/login.blade.php を表示
    }

    // ✅ ログイン処理
    public function login(Request $request)
    {
        // 他の guard をログアウト（admin → logout）
        auth('admin')->logout();

        // バリデーション
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // web guard でログイン
        if (auth('web')->attempt($credentials)) {
            $request->session()->regenerate();

            // メール認証チェック
            if (!auth('web')->user()->hasVerifiedEmail()) {
                auth('web')->logout(); // メール認証してないならログアウトするか、リダイレクトだけでもOK
                return redirect()->route('verification.notice');
            }

            return redirect('/attendance');
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ]);
    }

    // ✅ ログアウト処理
    public function logout(Request $request)
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}