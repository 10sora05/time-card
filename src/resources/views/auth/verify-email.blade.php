@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/email.css') }}">
@endsection

{{-- 独自ヘッダー（ロゴのみ） --}}
@section('custom_header')
<header class="header">
    <div class="header__inner">
        <a href="/attendance">
            <img class="header__logo" src="{{ asset('images/logo.svg') }}">
        </a>
    </div>
</header>
@endsection

@section('content')
<div class="content">
    <div class="email-form__content">
        <div class="form__group">
            <p class="email_p">登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p class="email_p">メール認証を完了してください。</p>

            <a href="https://mailtrap.io/home" class="email-link_btn" target="_blank" rel="noopener noreferrer">
            認証はこちらから
            </a>

            @if (session('status') == 'verification-link-sent')
            <p style="color: green;">新しい認証リンクを送信しました。</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
            @csrf
                <button type="submit" class="email_submit">認証メールを再送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection
