@extends('layouts.crypto')

@section('title', 'パスワードリマインド'. ' | Cryoto Trend')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">パスワードリセット</h1>
    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="c-form_contents">
            <div class="u-mb-lg">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="c-input_text u-mt-sm @error('email') c-invalid_input-text @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="c-invalid_text" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="u-ta-c">
                    <button type="submit" class="c-button c-button-peace u-mb-md">パスワードリセットメール送信</button>
            </div>
        </div>
    </form>
</div>
@endsection