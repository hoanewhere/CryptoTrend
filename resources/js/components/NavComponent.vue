<template>
<nav class="c-nav">
    <div class="c-nav_trigger" @click="navClick">
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
        <span class="c-nav_trigger-parts" :class="{'is-active': navActive}"></span>
    </div>
    <div class="c-nav-sm" v-show="navActive">
        <ul class="c-nav_ul">
            <li class="c-nav_li c-button c-button-warning" v-for="(nav, index) in navList" :key="index">
                <a :href="nav.url">{{ nav.title }}</a>
            </li>
        </ul>
    </div>
    <div class="c-nav-md">
        <ul class="c-nav_ul-md">
            <li class="u-ml-md" v-for="(nav, index) in navList" :key="index">
                <a :href="nav.url">{{ nav.title }}</a>
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
