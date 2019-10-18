<template>
<div>
    <slide-up-down :active="active" :dulation="10000">
        <div class="c-alert" :class="getAlertClass()">
            {{ message }}
        </div>
    </slide-up-down>
</div>
</template>

<script>
    const URL_ORIGIN = location.origin

    export default {
        props: {
            state: {
                required: true,
                type: Boolean,
            },
            message: {
                required: true,
                type: String,
            }
        },
        data: function () {
            return {
                active: false,
            }
        },
        mounted() {
            setTimeout(this.toggleIsDisplay, 500)
            setTimeout(this.toggleIsDisplay, 4000)
        },
        methods: {
            getAlertClass() { // 現在のページ番号にactiveをつける
                let classes = '';

                if(this.state == true) {
                    classes = 'c-alert-success';
                } else {
                    classes = 'c-alert-danger';
                }
                return classes;
            },
            toggleIsDisplay() { // 表示のトグル処理
                this.active = !this.active
            }
        }
        
    }
</script>
