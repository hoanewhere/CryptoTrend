@extends('layouts.crypto')

@section('content')
<h1 class="c-title_main">仮想通貨トレンドランキング</h1>
<div class="u-ta-c">
    <p class="u-mb-sm">データ取得時間：YYYY／MM／DD/HH/mm/ss</p>
    <button type="button" class="c-button c-button-peace">データ再取得</button>
</div>
<div class="p-trendRanking">
    <div class="c-search u-ta-c p-trendRanking_child">
        <h3 class="c-title_article u-mb-lg">表示条件</h3>
        <form action="">
            <div class="c-search_mono u-mb-lg">
                <label for="searchTime" class="c-form-title c-title_sub u-mb-sm">対象時間</label>
                <select name="" id="searchTime" class="c-input_select u-w50p u-mx-a">
                    <option :value="0">1時間</option>
                    <option :value="1">1日</option>
                    <option :value="1">1週間</option>
                </select>
            </div>
            <div class="c-search_mono u-mb-lg">
                <label for="" class="c-form-title c-title_sub u-mb-sm">表示銘柄</label>
                <div class="u-mb-sm u-d-fx u-ai-c u-jc-c">
                    <input type="radio" name="" id="showCryptoAll" class="c-input_rqdio u-mr-sm">
                    <label for="showCryptoAll">全て</label>
                </div>
                <div class="c-search_chks">
                    <div class="c-search_chk">
                        <input type="checkbox" name="" id="showCrypto1" value="BTC" class="c-input_checkbox u-mr-sm">
                        <label for="showCrypto1">BTC</label>
                    </div>
                    <div class="c-search_chk">
                        <input type="checkbox" name="" id="showCrypto2" value="AAA" class="u-mr-sm">
                        <label for="showCrypto2">AAA</label>
                    </div>
                    <div class="c-search_chk">
                        <input type="checkbox" name="" id="showCrypto2" value="AAA" class="u-mr-sm">
                        <label for="showCrypto2">BBB</label>
                    </div>
                    <div class="c-search_chk">
                        <input type="checkbox" name="" id="showCrypto2" value="AAA" class="u-mr-sm">
                        <label for="showCrypto2">CCC</label>
                    </div>
                    <div class="c-search_chk">
                        <input type="checkbox" name="" id="showCrypto2" value="AAA" class="u-mr-sm">
                        <label for="showCrypto2">DDD</label>
                    </div>
                </div>
            </div>
            <button type="button" class="c-button c-button-peace">表示</button>
        </form>
    </div>
    <div class="c-ranks p-trendRanking_child">
        <div class="c-rank" rank-cnt="1">
            <div class="c-rank_top">
                <h3 class="c-title_article">BTC</h3>
                <p>ツイート数：<span>777</span></p>
            </div>
            <div class="c-rank_buttom u-ta-c">
                <p class="u-mb-sm">取引価格(過去24時間)</p>
                <p>最高：<span>888.88</span></p>
                <p>最低：<span>111.11</span></p>
            </div>
        </div>
        <div class="c-rank" rank-cnt="2">
            <div class="c-rank_top">
                <h3 class="c-title_article">BTC</h3>
                <p>ツイート数：<span>777</span></p>
            </div>
            <div class="c-rank_buttom u-ta-c">
                <p class="u-mb-sm">取引価格(過去24時間)</p>
                <p>最高：<span>888.88</span></p>
                <p>最低：<span>111.11</span></p>
            </div>
        </div>
        <div class="c-rank" rank-cnt="3">
            <div class="c-rank_top">
                <h3 class="c-title_article">BTC</h3>
                <p>ツイート数：<span>777</span></p>
            </div>
            <div class="c-rank_buttom u-ta-c">
                <p class="u-mb-sm">取引価格(過去24時間)</p>
                <p>最高：<span>888.88</span></p>
                <p>最低：<span>111.11</span></p>
            </div>
        </div>
        <div class="c-rank" rank-cnt="4">
            <div class="c-rank_top">
                <h3 class="c-title_article">BTC</h3>
                <p>ツイート数：<span>777</span></p>
            </div>
            <div class="c-rank_buttom u-ta-c">
                <p class="u-mb-sm">取引価格(過去24時間)</p>
                <p>最高：<span>888.88</span></p>
                <p>最低：<span>111.11</span></p>
            </div>
        </div>
    </div>
</div>

@endsection