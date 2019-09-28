<template>
<nav class="c-nav">
    <ul class="c-nav_ul">
        <li class="u-ml-md" v-for="(nav, index) in navList" :key="index">
            <a :href="nav.url">{{ nav.title }}</a>
        </li>
    </ul>
</nav>
</template>

<script>
    export default {
        data: function () {
            return {
                navList: {}
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
            }
        }
    }
</script>
