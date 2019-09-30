<template>
<nav class="c-nav">
    <div class="c-nav_trigger" @click="navClick">
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
    </div>
    <div class="c-nav-sm" v-show="navActive">
        <ul class="c-nav_ul">
            <li class="c-nav_li" v-for="(nav, index) in navList" :key="index">
                <a v-if="nav.title != 'ログアウト'" :href="nav.url" class="c-button c-button-warning">{{ nav.title }}</a>
                <button v-else type="submit" class="c-button c-button-warning">{{ nav.title }}</button>
            </li>
        </ul>
    </div>
    <div class="c-nav-md">
        <ul class="c-nav_ul-md">
            <li class="c-nav_li" v-for="(nav, index) in navList" :key="index">
                <a v-if="nav.title != 'ログアウト'" :href="nav.url" class="c-button c-button-warning">{{ nav.title }}</a>
                <button v-else type="submit" class="c-button c-button-warning">{{ nav.title }}</button>
            </li>
        </ul>
    </div>
</nav>
</template>

<script>
    export default {
        data: function () {
            return {
                navList: {},
                navActive: false,
            }
        },
        mounted() {
            this.reloadData()
        },
        methods: {
            reloadData: function() {
                axios.get('/crypto/reloadNavData')
                .then((res) => {
                    this.navList = res.data
                })
            },
            navClick: function() {
                this.navActive = !this.navActive
            }
        }
    }
</script>
