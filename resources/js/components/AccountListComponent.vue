<template>
<div>
    <h1 class="c-title_main">仮想通貨アカウント一覧</h1>
    <div class="u-ta-c u-mb-lg">
        <p class="u-mb-sm">{{ gotTime }}</p>
    </div>
    <div class="u-ta-c u-mb-lg" v-if="accounts">
        <button v-if="loginFlg == false" type="button" disabled class="c-button c-button-dark" @click="toggleConnectedTwitter">ログイン後、機能解放</button>
        <button v-else-if="connectedTwitterFlg == true" type="button" class="c-button c-button-peace" @click="toggleConnectedTwitter">twitter連携中</button>
        <button v-else type="button" class="c-button c-button-dark" @click="toggleConnectedTwitter">twitter連携　開始</button>
    </div>
    <div class="u-ta-c u-mb-lg" v-if="accounts">
        <button v-if="connectedTwitterFlg == false" type="button" disabled class="c-button c-button-dark">連携後、機能解放</button>
        <button v-else-if="autoFollowFlg == true" type="button" class="c-button c-button-peace" @click="toggleAutoFollow">自動フォロー ON中</button>
        <button v-else type="button" class="c-button c-button-dark" @click="toggleAutoFollow">自動フォロー OFF</button>
    </div>

    <!-- ページネーション -->
    <pagination-component :data="accountsPaginate" @move-page="movePage($event)"></pagination-component>
    
    <!-- アカウント一覧 -->
    <div class="p-accounts">
        <div class="p-account" v-for="account in accounts" :key="account.id">
            <transition name="c-tra-fade" appear>
                <div class="p-account_inner">
                    <img :src="account.account_data.profile_image_url" alt="" class="p-account_inner-img">
                    <div class="p-account_inner-btn u-mb-md">
                        <button v-if="connectedTwitterFlg == false" type="button" disabled class="c-button c-button-dark">連携後、機能解放</button>
                        <button v-else-if="account.following == false" type="button" class="c-button c-button-peace" @click="toFollow(account)">フォローする</button>
                        <button v-else type="button" class="c-button c-button-dark" @click="unfollow(account)">フォロー済</button>
                    </div>
                    <slide-up-down :active="account.activeToggle" :dulation="10000">
                        <div class="c-alert u-mb-md" :class="getAlertClass(account)">
                            <p>{{account.message}}</p>
                        </div>
                    </slide-up-down>
                    <p class="u-mb-sm">{{ account.account_data.name }}</p>
                    <p class="u-mb-sm">{{ account.account_data.screen_name }}</p>
                    <div class="p-account_inner-row u-mb-sm">
                        <p class="u-mr-lg">フォロー<span>{{ account.account_data.friends_count }}</span></p>
                        <p>フォロワー<span>{{ account.account_data.followers_count }}</span></p>
                    </div>
                    <div class="u-mb-sm">
                        <p>{{ account.account_data.description }}</p>
                    </div>
                    <div v-html="account.account_data.latest_html"></div>
                </div>
            </transition>
        </div>
    </div>

    <!-- ページネーション -->
    <pagination-component :data="accountsPaginate" @move-page="movePage($event)"></pagination-component>

</div>
</template>

<script>
    export default {
        data: function () {
            return {
                accounts: {},
                accountsPaginate: {},
                gotTime: '',
                autoFollowFlg: false,
                connectedTwitterFlg: false,
                loginFlg: false,
                page: 1,
                message: "",
                isSuccess: false,
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
            reloadData: function() { // アカウントデータ取得
                const url = "/accountList/reloadTweetData?page=" + this.page

                axios.get(url)
                .then((res) => {
                    this.accounts = res.data.accounts.data
                    this.accountsPaginate = res.data.accounts
                    this.gotTime = res.data.got_time
                    this.autoFollowFlg = res.data.auto_follow_flg
                    this.connectedTwitterFlg = res.data.connected_twitter_flg
                    this.loginFlg = res.data.login_flg

                    // acocuntデータをjsonにパース
                    for(let i in this.accounts) {
                        let jsonData = JSON.parse(this.accounts[i].account_data)
                        this.accounts[i].account_data = jsonData
                    }
                })
            },
            toFollow: function(account) { // アカウントフォロー処理
                axios.post('accountList/toFollow', {
                    record_id: account.id,
                    screen_name: account.account_data.screen_name
                }).then((res) => {
                    account.isSuccess = res.data.follow_flg
                    account.following = res.data.follow_flg
                    if(account.isSuccess) {
                        account.message = 'フォローしました'
                    } else {
                        account.message = 'フォロー失敗しました。時間をおいて再度操作してください。'
                    }
                    
                    this.$set(account, "activeToggle", true)
                    setTimeout(() => {
                        this.toggleIsDisplay(account);
                    }, 4000);
                })
            },
            unfollow: function(account) { // アカウントフォロー解除処理
                axios.post('accountList/unfollow', {
                    record_id: account.id,
                    screen_name: account.account_data.screen_name
                }).then((res) => {
                    account.following = false;
                })
            },
            toggleAutoFollow: function() { // 自動フォローの切り替え処理
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
            },
            toggleConnectedTwitter: function() { // ツイッター連携の切り替え処理
                if(this.connectedTwitterFlg == true) { // 連携解除
                    axios.post('accountList/connectStop')
                    .then((res) => {
                        this.connectedTwitterFlg = false
                    })
                } else { // 連携開始
                    axios.post('accountList/connectStart')
                    .then((res) => {
                        // this.connectedTwitterFlg = true // 連携開始の場合、再度データが読み込まれるため、ここではフラグをONにしない
                        location.href = res.data
                    })
                }
            },
            movePage(page) { // ページネーションのページ移動処理
                this.page = page
                this.reloadData()
                window.scrollTo(0, 0)
            },
            getAlertClass(account) { // 現在のページ番号にactiveをつける
                let classes = '';

                if(account.isSuccess == true) {
                    classes = 'c-alert-success';
                } else {
                    classes = 'c-alert-danger';
                }
                return classes;
            },
            toggleIsDisplay: function(account) { // 表示のトグル処理
                this.$set(account, "activeToggle", false)

                // vue　data更新用の処理
                this.$set(account, "update", false)
                delete account.update
            }
        }
    }
</script>
