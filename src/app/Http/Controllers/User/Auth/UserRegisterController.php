<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class UserRegisterController extends Controller
{
    // 会員登録フォームの表示
    public function show()
    {
        return view('auth.user_register'); // Bladeファイルのパスに合わせて変更
    }

    // 登録処理
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録後にメール認証通知を送るためのイベントを発火
        event(new Registered($user));

        // すぐログインさせる（任意）
        Auth::guard('web')->login($user);

        // メール認証案内ページにリダイレクト
        return redirect()->route('verification.notice');
    }
}
