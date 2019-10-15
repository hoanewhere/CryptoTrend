<template>
<nav class="c-nav">

    <!-- ハンバーガーアイコン -->
    <div class="c-nav_trigger" @click="navClick">
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
    </div>

    <!-- スマホ時のNav -->
    <div class="c-nav_body" v-show="navActive">
        <ul class="c-nav_ul">
            <li class="c-nav_li" v-if="auth"><button type="submit" class="c-button c-button-warning">{{urlLogout.title}}</button></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlLogin.url" class="c-button c-button-warning">{{ urlLogin.title }}</a></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlRegister.url" class="c-button c-button-warning">{{ urlRegister.title }}</a></li>
            <li class="c-nav_li"><a :href="urlIndex.url" class="c-button c-button-warning">{{urlIndex.title}}</a></li>
            <li class="c-nav_li"><a :href="urlAccount.url" class="c-button c-button-warning">{{urlAccount.title}}</a></li>
            <li class="c-nav_li"><a :href="urlNews.url" class="c-button c-button-warning">{{urlNews.title}}</a></li>
        </ul>
    </div>

    <!-- タブレット、PC時のNav -->
    <div class="c-nav_body-md">
        <ul class="c-nav_ul-md">
            <li class="c-nav_li" v-if="auth"><button type="submit" class="c-button c-button-warning">{{urlLogout.title}}</button></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlLogin.url" class="c-button c-button-warning">{{ urlLogin.title }}</a></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlRegister.url" class="c-button c-button-warning">{{ urlRegister.title }}</a></li>
            <li class="c-nav_li"><a :href="urlIndex.url" class="c-button c-button-warning">{{urlIndex.title}}</a></li>
            <li class="c-nav_li"><a :href="urlAccount.url" class="c-button c-button-warning">{{urlAccount.title}}</a></li>
            <li class="c-nav_li"><a :href="urlNews.url" class="c-button c-button-warning">{{urlNews.title}}</a></li>
        </ul>
    </div>

</nav>
</template>

<script>
    const URL_ORIGIN = location.origin

    export default {
        props: {
            auth: {
                required: false
            }
        },
        data: function () {
            return {
                navList: {},
                navActive: false,
                urlIndex: {url: URL_ORIGIN + '/index', title: 'トレンド'},
                urlAccount: {url: URL_ORIGIN + '/accountList', title: 'アカウント一覧'},
                urlNews: {url: URL_ORIGIN + '/newsList', title: 'ニュース一覧'},
                urlLogin: {url: URL_ORIGIN + '/login', title: 'ログイン'},
                urlLogout: {url: URL_ORIGIN + '/logout', title: 'ログアウト'},
                urlRegister: {url: URL_ORIGIN + '/register', title: 'ユーザ登録'},
            }
        },
        methods: {
            navClick: function() { // ハンバーガーアイコン押下時の処理
                this.navActive = !this.navActive
            }
        }
    }
</script>
