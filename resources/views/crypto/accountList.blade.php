@extends('layouts.crypto')

@section('content')
<h1 class="c-title_main">仮想通貨アカウント一覧</h1>
<div class="u-ta-c u-mb-lg">
    <p class="u-mb-sm">データ取得時間：YYYY／MM／DD/HH/mm/ss</p>
    <button type="button" class="c-button c-button-peace">データ再取得</button>
</div>
<div class="u-ta-c u-mb-lg">
    <button type="button" class="c-button c-button-peace">自動フォロー OFF/ON</button>
</div>
<div class="accounts">
    <div class="account">
        <div class="account_inner">
            <img src="{{ asset('img/hihumin.jpg') }}" alt="" class="account_inner-img">
            <div class="account_inner-btn u-mb-md">
                <button type="button" class="c-button c-button-peace">フォローする</button>
            </div>
            <p class="u-mb-sm">ユーザ名</p>
            <p class="u-mb-sm">アカウント名</p>
            <div class="account_inner-row u-mb-sm">
                <p class="u-mr-lg">フォロー<span>888</span></p>
                <p>フォロワー<span>999</span></p>
            </div>
            <div class="u-mb-sm">
                <p>自己紹介あああああああああああああああああああああああああああああああああああああああああああああああああああああああ</p>
            </div>
            <div>
                最新ツイート
            </div>
        </div>
    </div>
    <div class="account">
        <div class="account_inner">
            <img src="{{ asset('img/hihumin.jpg') }}" alt="" class="account_inner-img">
            <div class="account_inner-btn u-mb-md">
                <button type="button" class="c-button c-button-peace">フォローする</button>
            </div>
            <p class="u-mb-sm">ユーザ名</p>
            <p class="u-mb-sm">アカウント名</p>
            <div class="account_inner-row u-mb-sm">
                <p class="u-mr-lg">フォロー<span>888</span></p>
                <p>フォロワー<span>999</span></p>
            </div>
            <div class="u-mb-sm">
                <p>自己紹介あああああああああああああああああああああああああああああああああああああああああああああああああああああああ</p>
            </div>
            <div>
                最新ツイート
            </div>
        </div>
    </div>
    <div class="account">
        <div class="account_inner">
            <img src="{{ asset('img/hihumin.jpg') }}" alt="" class="account_inner-img">
            <div class="account_inner-btn u-mb-md">
                <button type="button" class="c-button c-button-peace">フォローする</button>
            </div>
            <p class="u-mb-sm">ユーザ名</p>
            <p class="u-mb-sm">アカウント名</p>
            <div class="account_inner-row u-mb-sm">
                <p class="u-mr-lg">フォロー<span>888</span></p>
                <p>フォロワー<span>999</span></p>
            </div>
            <div class="u-mb-sm">
                <p>自己紹介ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ</p>
            </div>
            <div>
                最新ツイート
            </div>
        </div>
    </div>
    <div class="account">
        <div class="account_inner">
            <img src="{{ asset('img/hihumin.jpg') }}" alt="" class="account_inner-img">
            <div class="account_inner-btn u-mb-md">
                <button type="button" class="c-button c-button-peace">フォローする</button>
            </div>
            <p class="u-mb-sm">ユーザ名</p>
            <p class="u-mb-sm">アカウント名</p>
            <div class="account_inner-row u-mb-sm">
                <p class="u-mr-lg">フォロー<span>888</span></p>
                <p>フォロワー<span>999</span></p>
            </div>
            <div class="u-mb-sm">
                <p>自己紹介ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ</p>
            </div>
            <div>
                最新ツイート
            </div>
        </div>
    </div>
    <div class="account">
        <div class="account_inner">
            <img src="{{ asset('img/hihumin.jpg') }}" alt="" class="account_inner-img">
            <div class="account_inner-btn u-mb-md">
                <button type="button" class="c-button c-button-peace">フォローする</button>
            </div>
            <p class="u-mb-sm">ユーザ名</p>
            <p class="u-mb-sm">アカウント名</p>
            <div class="account_inner-row u-mb-sm">
                <p class="u-mr-lg">フォロー<span>888</span></p>
                <p>フォロワー<span>999</span></p>
            </div>
            <div class="u-mb-sm">
                <p>自己紹介ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ</p>
            </div>
            <div>
                最新ツイート
            </div>
        </div>
    </div>
</div>
@endsection