@extends('layouts.crypto')

@section('title', 'TOP'. ' | Cryoto Trend')

@section('content')
<div>

    <div class="p-top_block p-top_bg-crypto">
        <div class="p-top_container-top">
            <h1 class="p-top_title">Crypto Trend</h1>
            <p>本サービスは仮想通貨（Crypto）の流行（Trend）を様々なデータから追いかけることができます。</p>
        </div>
        <div class="p-top_cover"></div>
    </div>

    <div class="p-top_block">
        <div class="p-top_container">
            <h2 class="p-top_title-sub">トレンドランキング</h2>
            <div class="p-top_container-body">
                <div class="c-trim p-top_container-body-img">
                    <img src="{{ asset('img/analytics.jpg') }}" class="p-top_img c-trim-img">
                </div>
                <div class="p-top_container-body-text">
                    <p class="p-top_text">仮想通貨の各銘柄のツイート数をランキング形式でまとめています。</p>
                    <p class="p-top_text-focus">今１番熱い銘柄をCheck！！</p>
                    <a href="{{ url('/index') }}" class="c-button c-button-danger">トレンド</a>
                </div>
            </div>
        </div>
    </div>

    <div class="p-top_block p-top_bg-white">
        <div class="p-top_container">
            <h2 class="p-top_title-sub">アカウント一覧</h2>
            <div class="p-top_container-body p-top_container-body-reverse">
                <div class="c-trim p-top_container-body-img">
                    <img src="{{ asset('img/accountList.jpg') }}" class="p-top_img c-trim-img">
                </div>
                <div class="p-top_container-body-text">
                    <p class="p-top_text">仮想通貨に関連するツイッターユーザーを一覧にしています。（本サイトにログイン、ツイッター連携することでアカウント自動フォロー機能（下記参照）が使用可能になります）</p>
                    <p class="p-top_text-focus">仲間、情報をGet！!</p>
                    <a href="{{ url('/accountList') }}" class="c-button c-button-danger">アカウント一覧</a>
                </div>
            </div>
        </div>
    </div>

    <div class="p-top_block">
        <div class="p-top_container">
            <h2 class="p-top_title-sub">アカウント自動フォロー</h2>
            <div class="p-top_container-body">
                <div class="c-trim p-top_container-body-img">
                    <img src="{{ asset('img/automate.jpg') }}" class="p-top_img c-trim-img">
                </div>
                <div class="p-top_container-body-text">
                    <p class="p-top_text">アカウント一覧にあるツイッターユーザーを自動フォローすることができます（要ログイン、要ツイッター連携）。</p>
                    <p class="p-top_text-focus">面倒な処理はAutomate！！</p>
                    <a href="{{ url('/accountList') }}" class="c-button c-button-danger">アカウント一覧</a>
                </div>
            </div>
        </div>
    </div>

    <div class="p-top_block p-top_bg-white">
        <div class="p-top_container">
            <h2 class="p-top_title-sub">ニュース一覧</h2>
            <div class="p-top_container-body p-top_container-body-reverse">
                <div class="c-trim p-top_container-body-img">
                    <img src="{{ asset('img/news.jpg') }}" class="p-top_img c-trim-img">
                </div>
                <div class="p-top_container-body-text">
                    <p class="p-top_text">仮想通貨に関する最新ニュースを一覧にしています。</p>
                    <p class="p-top_text-focus">世界情勢も忘れずCheck！！</p>
                    <a href="{{ url('/newsList') }}" class="c-button c-button-danger">ニュース</a>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection