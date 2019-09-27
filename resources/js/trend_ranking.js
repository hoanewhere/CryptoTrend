const trend_ranking = new Vue({
    el: '#trend-ranking',
    data: {
        trends: [],
        filteredTrends: [],
        cryptoList: [],
        gotTime: '',
        selectedCryptoIds: [],
        selectedSearchTerm: 0,
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
            trendCnt = 0

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
});