<template>
<ul class="c-pagination" v-if="Object.keys(data).length">

    <!-- prevボタン -->
    <li class="c-pagination_item" v-if="hasPrev">
        <a class="c-pagination_link" href="" @click.prevent="move(data.current_page-1)">前へ</a>
    </li>

    <!-- ページボタン -->
    <li :class="getPageClass(page)" v-for="(page, index) in pages" :key="index">
        <a class="c-pagination_link" href="" @click.prevent="move(page)" v-text="page"></a>
    </li>

    <!-- nextボタン -->
    <li class="c-pagination_item" v-if="hasNext">
        <a class="c-pagination_link" href="" @click.prevent="move(data.current_page+1)">次へ</a>
    </li>

</ul>
</template>

<script>
    const MAX_SHOW_PAGE = 5

    export default {
        props: {
            data: {
                required: false
            },
        },
        data: function () {
            return {
                totalPage: 0,
                currentPage: 0,
            }
        },
        mounted() {
        },
        methods: {
            move(page) { // 各ボタン押下時、親側に処理を通知する
                if(this.data.current_page != page) {
                    this.$emit('move-page', page)
                }
            },
            getPageClass(page) { // 現在のページ番号にactiveをつける
                let classes = ['c-pagination_item'];

                if(this.data.current_page == page) {
                    classes.push('c-pagination_item-active');
                }
                return classes;
            }
        },
        computed: {
            hasPrev() { // prevボタンの表示、非表示
                return (this.data.prev_page_url != null);
            },
            hasNext() { // nextボタンの表示、非表示
                return (this.data.next_page_url != null);
            },
            pages() { // 表示するpage番号を取得
                let pages = [];

                if(this.data.last_page <= MAX_SHOW_PAGE) {
                    for(let i = 1 ; i <= this.data.last_page ; i++) {
                        pages.push(i)
                    }
                } else if(this.data.current_page == 1 || this.data.current_page == 2 ) {
                    for(let i = 1 ; i <= MAX_SHOW_PAGE ; i++) {
                        pages.push(i)
                    }
                } else if(this.data.current_page >= (this.data.last_page - 1)) {
                    let cnt = this.data.last_page - MAX_SHOW_PAGE

                    for(let i = 0 ; i < MAX_SHOW_PAGE ; i++) {
                        cnt++
                        pages.push(cnt)
                    }
                } else {
                    let cnt = this.data.current_page - 3

                    for(let i = 0 ; i < MAX_SHOW_PAGE ; i++) {
                        cnt++
                        pages.push(cnt)
                    }
                }
                return pages;
            }
        },
    }
</script>
