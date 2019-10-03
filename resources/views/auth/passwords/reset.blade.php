@extends('layouts.crypto')

@section('title', 'パスワードリマインド')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">パスワードリセット</h1>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <div class="c-form_contents">
            <div class="u-mb-lg">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" class="c-input_text u-mt-sm @error('email') c-invalid_input-text @enderror" required autocomplete="email" autofocus>

                @error('email')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="c-input_text u-mt-sm @error('password') is-invalid @enderror" required autocomplete="new-password">

                @error('password')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-mb-lg">
                <label for="password-confirm">Re:パスワード</label>
                <input type="password" name="password_confirmation" id="password-confirm" class="c-input_text u-mt-sm" required autocomplete="new-password">
            </div>
            <div class="u-ta-c">
                <button type="submit" class="c-button c-button-peace u-mb-md">パスワードリセット</button>
            </div>
        </div>
    </form>
</div>
@endsection