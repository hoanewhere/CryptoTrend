@extends('layouts.crypto')

@section('content')
<div id="account-list">
    <h1 class="c-title_main">仮想通貨アカウント一覧</h1>
    <div class="u-ta-c u-mb-lg">
        <p class="u-mb-sm">データ取得時間：@{{ gotTime }}</p>
        {{-- <button type="button" class="c-button c-button-peace">データ再取得</button> --}}
    </div>
    <div class="u-ta-c u-mb-lg">
        <button type="button" class="c-button c-button-peace">自動フォロー OFF/ON</button>
    </div>
    <div class="accounts">
        <div class="account" v-for="account in accounts" :key="account.id">
            <div class="account_inner">
                <img :src="account.account_data.profile_image_url" alt="" class="account_inner-img">
                <div class="account_inner-btn u-mb-md">
                    <button v-if="account.account_data.following === false" type="button" class="c-button c-button-peace" @click="toFollow(account)">フォローする</button>
                    <button v-else type="button" class="c-button c-button-dark" @click="unfollow(account)">フォロー済</button>
                </div>
                <p class="u-mb-sm">@{{ account.account_data.name }}</p>
                <p class="u-mb-sm">@{{ account.account_data.screen_name }}</p>
                <div class="account_inner-row u-mb-sm">
                    <p class="u-mr-lg">フォロー<span>@{{ account.account_data.friends_count }}</span></p>
                    <p>フォロワー<span>@{{ account.account_data.followers_count }}</span></p>
                </div>
                <div class="u-mb-sm">
                    <p>@{{ account.account_data.description }}</p>
                </div>
                <div v-html="account.account_data.latest_html"></div>
            </div>
        </div>
    </div>
</div>
@endsection