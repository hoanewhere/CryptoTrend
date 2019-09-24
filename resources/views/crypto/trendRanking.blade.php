@extends('layouts.crypto')

@section('content')
<div id="trend-ranking">
    <h1 class="c-title_main">仮想通貨トレンドランキング</h1>
    <div class="u-ta-c">
        <p class="u-mb-sm">データ取得時間：@{{ gotTime }}</p>
        {{-- <button type="button" class="c-button c-button-peace">データ再取得</button> --}}
    </div>
    <div class="p-trendRanking">

        {{-- search --}}
        <div class="c-search u-ta-c p-trendRanking_child">
            <h3 class="c-title_article u-mb-lg">表示条件</h3>
            <form action="">
                <div class="c-search_mono u-mb-lg">
                    <label for="searchTime" class="c-form-title c-title_sub u-mb-sm">対象時間</label>
                    <select name="" id="searchTime" v-model="selectedSearchTerm" class="c-input_select u-w50p u-mx-a">
                        <option :value=0>1時間</option>
                        <option :value=1>1日</option>
                        <option :value=2>1週間</option>
                    </select>
                </div>
                <div class="c-search_mono u-mb-lg">
                    <label for="" class="c-form-title c-title_sub u-mb-sm">表示銘柄</label>
                    <div class="c-search_chks">
                        <div class="c-search_chk u-mb-xs">
                            <label><input type="checkbox" id="showCryptoAll" value="全て" class="c-input_checkbox u-mr-sm"　v-model="selectAll">全て</label>
                        </div>
                        <div v-for="crypto in cryptoList" class="c-search_chk u-mb-xs">
                            <label><input type="checkbox" :value="crypto.id" v-model="selectedCryptoIds" class="c-input_checkbox u-mr-sm">@{{ crypto.crypto }}</label>
                        </div>
                    </div>
                </div>
                <button type="button" class="c-button c-button-peace" @click="reloadData">表示</button>
            </form>
        </div>

        {{-- ranking --}}
        <div class="c-ranks p-trendRanking_child">
            <div class="c-rank" :rank-cnt="trend.rank" v-for="(trend, index) in filteredTrends">
                <div class="c-rank_top u-ta-c u-mb-md">
                    <h3 class="c-title_article">@{{ trend.crypto.crypto }}</h3>
                    <p>ツイート数：<span>@{{ trend.tweet_cnt }}</span></p>
                </div>
                <div class="c-rank_buttom u-ta-c">
                    <p class="u-mb-sm">取引価格(過去24時間/単位：円)</p>
                    <div class="u-d-fx u-jc-c">
                        <p class="u-mr-lg">最高：<span>@{{ trend.transaction_price_max }}</span></p>
                        <p>最低：<span>@{{ trend.transaction_price_min }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection