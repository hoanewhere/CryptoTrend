<template>
<div>
    <h1 class="c-title_main">仮想通貨トレンドランキング</h1>
    <div class="u-ta-c">
        <p class="u-mb-sm">データ取得時間：{{ gotTime }}</p>
        <button type="button" class="c-button c-button-peace p-search_button-dispaly" @click="searchClick">表示条件</button>
    </div>
    <div class="p-trendRanking">

        <!-- search -->
        <div class="p-search u-ta-c p-trendRanking_child" :class="{'is-active': searchActive}">
            <h3 class="c-title_article u-mb-lg">表示条件</h3>
            <form>
                <div class="p-search_mono u-mb-lg">
                    <label for="searchTime" class="c-form-title c-title_sub u-mb-sm">対象時間</label>
                    <select name="" id="searchTime" v-model="selectedSearchTerm" class="c-input_select u-w80p u-mx-a">
                        <option :value=0>1時間</option>
                        <option :value=1>1日</option>
                        <option :value=2>1週間</option>
                    </select>
                </div>
                <div class="p-search_mono u-mb-sm">
                    <label for="" class="c-form-title c-title_sub u-mb-sm">表示銘柄</label>
                    <div class="p-search_chks">
                        <div class="p-search_chk u-mb-md">
                            <label><input type="checkbox" id="showCryptoAll" value="全て" class="c-input_checkbox u-mr-sm" v-model="selectAll">全て</label>
                        </div>
                        <div v-for="crypto in cryptoList" :key="crypto.id" class="p-search_chk u-mb-md">
                            <label><input type="checkbox" :value="crypto.id" v-model="selectedCryptoIds" class="c-input_checkbox u-mr-sm">{{ crypto.crypto }}</label>
                        </div>
                    </div>
                </div>
                <button type="button" class="c-button c-button-peace p-search_button-search u-mb-md" @click="reloadTrendData">表示</button>
            </form>
        </div>

        <!-- ranking -->
        <div class="p-ranks p-trendRanking_child">
            <transition-group name="c-tra-slide-fade" appear>
                <div class="p-rank" :rank-cnt="trend.rank" v-for="trend in filteredTrends" :key="trend.id">
                    <div class="p-rank_top u-ta-c u-mb-md">
                        <h3 class="c-title_article u-mb-sm">{{ trend.crypto.crypto }}</h3>
                        <p>ツイート数：<span>{{ trend.tweet_cnt }}</span></p>
                    </div>
                    <div class="p-rank_buttom u-ta-c">
                        <p class="u-mb-sm">取引価格(過去24時間/単位：円)</p>
                        <div class="u-d-fx u-jc-c">
                            <p class="u-mr-lg">最高：<span>{{ trend.transaction_price_max }}</span></p>
                            <p>最低：<span>{{ trend.transaction_price_min }}</span></p>
                        </div>
                    </div>
                    <a :href="trend.search_url" class="p-rank-link"></a>
                </div>
            </transition-group>
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
                selectedSearchTerm: 0,
                searchActive: false,
            }
        },
        mounted() {
            this.reloadTrendData(true)
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
            reloadTrendData: function(first = false) { // トレンドデータを取得
                axios.get('/index/reloadTrendData/' + this.selectedSearchTerm)
                .then((res) => {
                    this.trends = res.data.trends
                    this.cryptoList = res.data.crypto_list
                    this.gotTime = res.data.got_time

                    if(first === true) {
                        this.selectedCryptoIds = []
                        for(var crypto of this.cryptoList) {
                            this.selectedCryptoIds.push(crypto.id)
                        }
                    }
                    
                    this.display()
                    this.searchActive = false
                })
            },
            display: function() {　// 選択された対象期間で表示するツイート数を切り替える
                const search_url_front = 'https://twitter.com/search?q='
                const search_url_back = '&src=typed_query&f=live'
                let trendCnt = 0
                this.filteredTrends = []
                

                this.trends.forEach((trend) => {
                    this.selectedCryptoIds.forEach((cryptoId) => {
                        if(cryptoId === trend.crypto.id) {
                            this.filteredTrends[trendCnt] = trend

                            // ランク設定
                            this.filteredTrends[trendCnt]['rank'] = trendCnt + 1

                            // 検索先のurl設定
                            this.filteredTrends[trendCnt]['search_url'] = search_url_front + trend.crypto.crypto + search_url_back

                            //表示するツイート数設定
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
            },
            searchClick: function() { // 検索画面の表示
                this.searchActive = true
            }
        }
    }
</script>
