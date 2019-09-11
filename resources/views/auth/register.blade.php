@extends('layouts.crypto')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">ユーザー登録</h1>
    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="c-form_contents">
            <div class="u-mb-lg">
                <label for="email" class="u-mb-sm">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="c-input_text @error('email') c-invalid_input-text @enderror" required autocomplete="email" autofocus>

                @error('email')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="password" class="u-mb-sm">パスワード</label>
                <input type="password" name="password" id="password" class="c-input_text @error('password') is-invalid @enderror" required autocomplete="new-password">

                @error('password')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="password-confirm" class="u-mb-sm">Re:パスワード</label>
                <input type="password" name="password_confirmation" id="password-confirm" class="c-input_text" required autocomplete="new-password">
            </div>
            <div class="u-mb-lg">
                <label for="twitter-id" class="u-mb-sm">Twitter アカウント</label>
                <input type="text" name="twitter-id" id="twitter-id" class="c-input_text">

                @error('twitter-id')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="twitter-pass" class="u-mb-sm">Twitter パスワード</label>
                <input type="password" name="twitter-pass" id="twitter-pass" class="c-input_text">

                @error('twitter-pass')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="twitter-pass-confirm" class="u-mb-sm">Twitter Re:パスワード</label>
                <input type="password" name="twitter-pass_confirmation" id="twitter-pass-confirm" class="c-input_text">
            </div>
            <div class="u-mb-lg u-ta-c">
                <button type="submit" class="c-button c-button-peace u-mb-md">新規登録</button>
            </div>
        </div>
    </form>
</div>
@endsection