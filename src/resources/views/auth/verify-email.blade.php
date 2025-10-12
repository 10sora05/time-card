<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メール認証のお願い</title>
</head>
<body>
    <h1>メール認証のお願い</h1>

    <p>登録ありがとうございます！</p>
    <p>メールで送信されたリンクをクリックして、メールアドレスを認証してください。</p>

    @if (session('status') == 'verification-link-sent')
        <p style="color: green;">新しい認証リンクを送信しました。</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">認証メールを再送信する</button>
    </form>
</body>
</html>
