<template>
<div>
    <h1 class="c-title_main">仮想通貨関連ニュース</h1>

    <div class="p-news">
        <div class="p-news_mono">
            <h3 class="p-news_mono-title">タイトル</h3>
            <p class="p-news_mono-sub">YYYY/MM/DD hh:mm:ss</p>
            <p class="p-news_mono-sub">引用元</p>
            <a class="p-news_mono-link" href=""></a>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data: function () {
            return {
                trends: [],
                filteredTrends: [],
                cryptoList: [],
                gotTime: '',
                selectedCryptoIds: [],
                selectedSearchTerm: 0
            }
        },
        mounted() {
            this.reloadTrendData()
        },
        computed: {
            selectAll: {
                get: function() {
                    if(this.selectedCryptoIds.length === this.cryptoList.length) {
                        return true
                    } else {
                        return false
                    }
                },
                set: function(checked) {
                    if (checked) {
                        this.selectedCryptoIds = []
                        for(var crypto of this.cryptoList) {
                            this.selectedCryptoIds.push(crypto.id)
                        }
                    } else {
                        this.selectedCryptoIds = []
                    }
                }
            }
        },
        methods: {
            reloadTrendData: function() {
                axios.get('/index/reloadTrendData/' + this.selectedSearchTerm)
                .then((res) => {
                    this.trends = res.data.trends
                    this.cryptoList = res.data.crypto_list
                    this.gotTime = res.data.got_time
                    this.selectedCryptoIds = []
                    for(var crypto of this.cryptoList) {
                        this.selectedCryptoIds.push(crypto.id)
                    }
                    this.display()
                })
            },
            display: function() {
                this.filteredTrends = []
                let trendCnt = 0

                this.trends.forEach((trend) => {
                    this.selectedCryptoIds.forEach((cryptoId) => {
                        if(cryptoId === trend.crypto.id) {
                            this.filteredTrends[trendCnt] = trend
                            this.filteredTrends[trendCnt]['rank'] = trendCnt + 1
                            switch (this.selectedSearchTerm) {
                                case 0:
                                    this.filteredTrends[trendCnt]['tweet_cnt'] = trend.hour_cnt
                                    break;
                                case 1:
                                    this.filteredTrends[trendCnt]['tweet_cnt'] = trend.day_cnt
                                    break;
                                case 2:
                                    this.filteredTrends[trendCnt]['tweet_cnt'] = trend.week_cnt
                                    break;
                                default:
                                        this.filteredTrends[trendCnt]['tweet_cnt'] = 0
                                    break;
                            }
                            trendCnt++
                        }
                    })
                })
            }
        }
    }
</script>
