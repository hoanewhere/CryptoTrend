<template>
<nav class="c-nav">

    <!-- ハンバーガーアイコン -->
    <div class="c-nav_trigger" @click="navClick(); getWindow()" >
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
    </div>

    <!-- スマホ,タブレット時のNav -->
    <div class="c-nav_body" v-show="navActive">
        <ul class="c-nav_ul" :style="{height: maxHeight}">
            <li class="c-nav_li" v-if="auth"><button type="submit" class="c-button c-button-danger">{{urlLogout.title}}</button></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlLogin.url" class="c-button c-button-danger">{{ urlLogin.title }}</a></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlRegister.url" class="c-button c-button-danger">{{ urlRegister.title }}</a></li>
            <li class="c-nav_li"><a :href="urlIndex.url" class="c-button c-button-danger">{{urlIndex.title}}</a></li>
            <li class="c-nav_li"><a :href="urlAccount.url" class="c-button c-button-danger">{{urlAccount.title}}</a></li>
            <li class="c-nav_li"><a :href="urlNews.url" class="c-button c-button-danger">{{urlNews.title}}</a></li>
        </ul>
    </div>

    <!-- PC時のNav -->
    <div class="c-nav_body-lg">
        <ul class="c-nav_ul-lg">
            <li class="c-nav_li" v-if="auth"><button type="submit" class="c-button c-button-danger">{{urlLogout.title}}</button></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlLogin.url" class="c-button c-button-danger">{{ urlLogin.title }}</a></li>
            <li class="c-nav_li" v-if="!(auth)"><a :href="urlRegister.url" class="c-button c-button-danger">{{ urlRegister.title }}</a></li>
            <li class="c-nav_li"><a :href="urlIndex.url" class="c-button c-button-danger">{{urlIndex.title}}</a></li>
            <li class="c-nav_li"><a :href="urlAccount.url" class="c-button c-button-danger">{{urlAccount.title}}</a></li>
            <li class="c-nav_li"><a :href="urlNews.url" class="c-button c-button-danger">{{urlNews.title}}</a></li>
        </ul>
    </div>

</nav>
</template>

<script>
    const URL_ORIGIN = location.origin

    export default {
        props: {
            auth: {
                required: false,
            }
        },
        data: function () {
            return {
                navList: {},
                navActive: false,
                urlIndex: {url: URL_ORIGIN + '/index', title: 'トレンドランキング'},
                urlAccount: {url: URL_ORIGIN + '/accountList', title: '仮想通貨アカウント一覧'},
                urlNews: {url: URL_ORIGIN + '/newsList', title: 'ニュース一覧'},
                urlLogin: {url: URL_ORIGIN + '/login', title: 'ログイン'},
                urlLogout: {url: URL_ORIGIN + '/logout', title: 'ログアウト'},
                urlRegister: {url: URL_ORIGIN + '/register', title: 'ユーザ登録'},
                heightPx: window.innerHeight,
            }
        },
        mounted() {
            window.addEventListener('resize', this.getWindow, false);
            this.getWindow();
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.getWindow, false);
        },
        computed: {
            maxHeight: function() {
                return this.heightPx + 'px'
            }
        },
        methods: {
            navClick: function() { // ハンバーガーアイコン押下時の処理
                this.navActive = !this.navActive
            },
            getWindow: function() {
              this.heightPx=window.innerHeight;
              console.log('windowの高さ:'+this.heightPx);
            },
        }
    }
</script>
