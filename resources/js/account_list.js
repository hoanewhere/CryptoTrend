const account_list = new Vue({
    el: '#account-list',
    data: {
        accounts: {},
        gotTime: '',
    },
    mounted() {
        this.reloadData()
        // twttr.widgets.load()
    },
    updated() {
        twttr.widgets.load()
    },
    beforeDestroy() {
        twttr.widgets.load()
    },
    computed: {

    },
    methods: {
        reloadData: function() {
            axios.get('/accountList/reloadTweetData/')
            .then((res) => {
                this.accounts = res.data.accounts
                this.gotTime = res.data.got_time

                // acocuntデータをjsonにパース
                for(let i in this.accounts) {
                    jsonData = JSON.parse(this.accounts[i].account_data)
                    this.accounts[i].account_data = jsonData
                }
            })
        },

    }
});