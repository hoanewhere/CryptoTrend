<template>
<div>
    <h1 class="c-title_main">仮想通貨関連ニュース</h1>

    <div class="p-news">
        <div class="p-news_mono" v-for="(news, index) in newsList" :key="index">
            <h3 class="p-news_mono-title">{{ news.title }}</h3>
            <p class="p-news_mono-sub">{{ news.date }}</p>
            <p class="p-news_mono-sub">{{ news.source }}</p>
            <a class="p-news_mono-link" :href="news.url"></a>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data: function () {
            return {
                newsList: [],
            }
        },
        mounted() {
            this.reloadNewsData()
        },
        computed: {
            
        },
        methods: {
            reloadNewsData: function() {
                axios.get('/newsList/reloadNewsData')
                .then((res) => {
                    this.newsList = res.data
                })
            }
        }
    }
</script>
