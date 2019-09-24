
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app'
// });


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
        this.reloadData()
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
        reloadData: function() {
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