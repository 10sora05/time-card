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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
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