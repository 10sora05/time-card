<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    // メール認証通知表示ページ
    public function showNotice()
    {
        return view('auth.verify-email');
    }

    // メール認証リンク処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('/attendance');
    }

    // メール認証再送信処理
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return redirect('/attendance')->with('message', '認証メールを再送信しました。');
    }

}
