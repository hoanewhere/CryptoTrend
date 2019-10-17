<!DOCTYPE html>
<html lang="jp">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Title --}}
    <title>@yield('title')</title>

    {{-- Description --}}
    <meta name="description" content="仮想通貨（Crypto）の流行（Trend）を様々なデータから追いかけることができます。ツイッターと連携することで、対象アカウントの自動フォロー等の処理などもあります。"/>

    {{-- Keywords --}}
    <meta name="keywords" content="仮想通貨,トレンド,流行" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Scripts --}}
    <script src="//platform.twitter.com/widgets.js" charset="utf-8" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    {{-- Styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="crypto-trend" class="l-body">
        
        {{-- header --}}
        <header class="l-header u-px-md">
            <div>
                <a href="{{ url('/top') }}"><img src="{{ asset('img/logo.png') }}" class="p-logo"></a>
            </div>
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                
                <nav-component :auth="{{ Auth::check() ? 'true' : 'false' }}"></nav-component>
            </form>
        </header>

        {{-- main --}}
        <main class="l-header_pt l-body_main">
            <div>
                @if (session('status'))
                    <message-component :state="true" message="{{ session('status') }}"></message-component>
                @endif
                @if (session('success'))
                    <message-component :state="true" message="{{ session('success') }}"></message-component>
                @endif
                @if (session('ng'))
                    <message-component :state="false" message="{{ session('ng') }}"></message-component>
                @endif
            </div>

            @yield('content')
        </main>

        {{-- footer --}}
        <footer class="l-footer">
            <div>
                <a href="{{ url('/top') }}"><img src="{{ asset('img/logo.png') }}" class="p-logo"></a>
            </div>
            <p>Copyright © Crypto Trend. All Rights Reserved</p>
        </footer>
    </div>
</body>
</html>