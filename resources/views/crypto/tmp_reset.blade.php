@extends('layouts.crypto')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">パスワードリセット</h1>
    <form action="">
        <div class="c-form_contents">
            <div class="u-mb-lg">
                <label for="email" class="u-mb-sm">Email</label>
                <input type="text" name="" id="email" class="c-input_text">
            </div>
            <div class="u-mb-lg">
                <label for="password" class="u-mb-sm">パスワード</label>
                <input type="password" name="" id="password" class="c-input_text">
            </div>
            <div class="u-mb-lg">
                <label for="rePassword" class="u-mb-sm">Re:パスワード</label>
                <input type="password" name="" id="rePassword" class="c-input_text">
            </div>
            <div class="u-mb-lg">
                <button type="submit" class="c-button c-button-peace">パスワードリセット</button>
            </div>
        </div>
    </form>
</div>
@endsection