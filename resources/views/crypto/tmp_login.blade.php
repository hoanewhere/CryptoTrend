@extends('layouts.crypto')

@section('content')
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
@endsection