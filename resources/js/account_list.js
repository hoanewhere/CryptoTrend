const account_list = new Vue({
    el: '#account-list',
    data: {
        accounts: {},
        gotTime: '',
        autoFollowFlg: false,
    },
    mounted() {
        this.reloadData()
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
                this.autoFollowFlg = res.data.auto_follow_flg

                // acocuntデータをjsonにパース
                for(let i in this.accounts) {
                    jsonData = JSON.parse(this.accounts[i].account_data)
                    this.accounts[i].account_data = jsonData
                }
            })
        },
        toFollow: function(account) {
            axios.post('accountList/toFollow', {
                record_id: account.id,
                screen_name: account.account_data.screen_name
            }).then((res) => {
                account.account_data.following = true;
            })
        },
        unfollow: function(account) {
            axios.post('accountList/unfollow', {
                record_id: account.id,
                screen_name: account.account_data.screen_name
            }).then((res) => {
                account.account_data.following = false;
            })
        },
        toggleAutoFollow: function() {
            if(this.autoFollowFlg === true) {
                this.autoFollowFlg = false
            } else {
                this.autoFollowFlg = true
            }
            axios.post('accountList/toggleAutoFollow', {
                auto_flg: this.autoFollowFlg
            }).then((res) => {
                
            })
        }
    }
});