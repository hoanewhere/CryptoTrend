<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Crypto Trend') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    {{-- header --}}
    <header class="l-header u-px-md">
        <div>
            <a href="{{ url('/') }}"><img src="{{ asset('img/logo.png') }}"></a>
        </div>
        <nav class="c-nav">
            <ul class="c-nav_ul">
                <li class="u-ml-md"><a href="">ログイン</a></li>
                <li class="u-ml-md"><a href="">ユーザー登録</a></li>
            </ul>
        </nav>
    </header>

    {{-- main --}}
    <main class="">
        <div class="c-form">
            <h1 class="c-form_title u-fz-lg">ログイン</h1>
            <form action="">
                <div class="c-form_contents">
                    <div class="u-mb-lg">
                        <label for="email" class="u-mb-sm">Email</label>
                        <input type="text" name="" id="email" class="c-form_input">
                    </div>
                    <div class="u-mb-lg">
                        <label for="password" class="u-mb-sm">パスワード</label>
                        <input type="password" name="" id="password" class="c-form_input">
                    </div>
                    <div class="c-form_content-row u-mb-lg">
                        <input type="checkbox" name="" id="remember" class="u-mr-md">
                        <label for="remember">ログイン状態を保持する</label>
                    </div>
                    <div class="c-form_content-row u-mb-lg">
                        <button type="submit" class="c-button c-button-peace u-mr-md u-mb-md">ログイン</button>
                        <a href="">パスワードを忘れた方はコチラ</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    {{-- footer --}}
    <footer>

    </footer>
</body>
</html>