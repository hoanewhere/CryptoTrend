<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Crypto Trend') }}</title>

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
                <a href="{{ url('/index') }}"><img src="{{ asset('img/logo.png') }}"></a>
            </div>
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                
                <nav-component></nav-component>
            </form>
        </header>

        {{-- main --}}
        <main class="l-header_pt l-body_main">
            @yield('content')
        </main>

        {{-- footer --}}
        <footer class="l-footer">
            <div>
                <a href="{{ url('/index') }}"><img src="{{ asset('img/logo.png') }}"></a>
            </div>
            <p>Copyright © Crypto Trend. All Rights Reserved</p>
        </footer>
    </div>
</body>
</html>