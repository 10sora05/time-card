<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Management</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
        <a href="/admin/attendances">
          <img class="header__logo" src="{{ asset('images/logo.svg') }}">
        </a>
        <nav>
          <ul class="header-nav">
            @if (Auth::guard('admin')->check())
              <li class="header-nav__item">
                <a class="header-nav__link" href="/admin/attendances">勤怠一覧</a>
              </li>
              <li class="header-nav__item">
                <a class="header-nav__link" href="/admin/users">スタッフ一覧</a>
              </li>
              <li class="header-nav__item">
                <a class="header-nav__link" href="/admin/requests">申請一覧</a>
              <li class="header-nav__item">
                <form class="form" action="{{ route('admin.logout') }}" method="post">
                  @csrf
                  <button class="header-nav__button">ログアウト</button>
                </form>
              </li>
            @endif
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
  @stack('scripts')
</body>

</html>
