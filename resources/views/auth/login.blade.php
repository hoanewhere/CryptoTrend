@extends('layouts.crypto')

@section('title', 'ログイン')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">ログイン</h1>
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="c-form_contents">
            <div class="u-mb-lg">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="c-input_text u-mt-sm @error('email') c-invalid_input-text @enderror" required autocomplete="email" autofocus>

                @error('email')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="c-input_text u-mt-sm @error('password') is-invalid @enderror" required autocomplete="current-password">

                @error('password')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="c-form_content-row u-mb-lg">
                <input type="checkbox" name="remember" id="remember" class="u-mr-md" {{ old('remember') ? 'checkd' : '' }}>
                <label for="remember">ログイン状態を保持する</label>
            </div>
            <div class="c-form_content-row">
                <button type="submit" class="c-button c-button-peace u-mb-md">ログイン</button>
                <a href="{{ route('password.request') }}" class="c-button c-button_link u-mb-md">パスワードを忘れた方</a>
            </div>
        </div>
    </form>
</div>
@endsection