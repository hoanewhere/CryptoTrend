<template>
<div>
    <h1 class="c-title_main">仮想通貨アカウント一覧</h1>
    <div class="u-ta-c u-mb-lg">
        <p class="u-mb-sm">データ取得時間：{{ gotTime }}</p>
    </div>
    <div class="u-ta-c u-mb-lg">
        <button v-if="autoFollowFlg == true" type="button" class="c-button c-button-peace" @click="toggleAutoFollow">自動フォロー ON</button>
        <button v-else type="button" class="c-button c-button-dark" @click="toggleAutoFollow">自動フォロー OFF</button>
    </div>
    <div class="accounts">
        <div class="account" v-for="account in accounts" :key="account.id">
            <div class="account_inner">
                <img :src="account.account_data.profile_image_url" alt="" class="account_inner-img">
                <div class="account_inner-btn u-mb-md">
                    <button v-if="account.account_data.following === false" type="button" class="c-button c-button-peace" @click="toFollow(account)">フォローする</button>
                    <button v-else type="button" class="c-button c-button-dark" @click="unfollow(account)">フォロー済</button>
                </div>
                <p class="u-mb-sm">{{ account.account_data.name }}</p>
                <p class="u-mb-sm">{{ account.account_data.screen_name }}</p>
                <div class="account_inner-row u-mb-sm">
                    <p class="u-mr-lg">フォロー<span>{{ account.account_data.friends_count }}</span></p>
                    <p>フォロワー<span>{{ account.account_data.followers_count }}</span></p>
                </div>
                <div class="u-mb-sm">
                    <p>{{ account.account_data.description }}</p>
                </div>
                <div v-html="account.account_data.latest_html"></div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data: function () {
            return {
                accounts: {},
                gotTime: '',
                autoFollowFlg: false
            }
        },
        mounted() {
            this.reloadData()
        },
        updated() {
            twttr.widgets.load()
        },
        beforeDestroy() {
            twttr.widgets.load()
        },
        computed: {

        },
        methods: {
            reloadData: function() {
                axios.get('/accountList/reloadTweetData/')
                .then((res) => {
                    this.accounts = res.data.accounts
                    this.gotTime = res.data.got_time
                    this.autoFollowFlg = res.data.auto_follow_flg

                    // acocuntデータをjsonにパース
                    for(let i in this.accounts) {
                        let jsonData = JSON.parse(this.accounts[i].account_data)
                        this.accounts[i].account_data = jsonData
                    }
                })
            },
            toFollow: function(account) {
                axios.post('accountList/toFollow', {
                    record_id: account.id,
                    screen_name: account.account_data.screen_name
                }).then((res) => {
                    account.account_data.following = true;
                })
            },
            unfollow: function(account) {
                axios.post('accountList/unfollow', {
                    record_id: account.id,
                    screen_name: account.account_data.screen_name
                }).then((res) => {
                    account.account_data.following = false;
                })
            },
            toggleAutoFollow: function() {
                if(this.autoFollowFlg == true) {
                    this.autoFollowFlg = false
                } else {
                    this.autoFollowFlg = true
                }
                axios.post('accountList/toggleAutoFollow', {
                    auto_flg: this.autoFollowFlg
                }).then((res) => {
                    console.log('toggleautofollow完了')
                })
            }
        }
    }
</script>
