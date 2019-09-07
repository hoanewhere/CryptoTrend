@extends('layouts.crypto')

@section('content')
<div class="c-form">
    <h1 class="c-form_title u-fz-lg">ユーザー登録</h1>
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
            <div class="u-mb-lg">
                <label for="rePassword" class="u-mb-sm">Re:パスワード</label>
                <input type="password" name="" id="rePassword" class="c-form_input">
            </div>
            <div class="u-mb-lg">
                <label for="twitterId" class="u-mb-sm">Twitter アカウント</label>
                <input type="text" name="" id="twitterId" class="c-form_input">
            </div>
            <div class="u-mb-lg">
                <label for="twitterPass" class="u-mb-sm">Twitter パスワード</label>
                <input type="password" name="" id="twitterPass" class="c-form_input">
            </div>
            <div class="u-mb-lg">
                <label for="twitterRePass" class="u-mb-sm">Twitter Re:パスワード</label>
                <input type="password" name="" id="twitterRePass" class="c-form_input">
            </div>
            <div class="u-mb-lg">
                <button type="submit" class="c-button c-button-peace">新規登録</button>
            </div>
        </div>
    </form>
</div>
@endsection