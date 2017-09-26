<template>
    <div class="search-history">
        <div class="header">
            <span class="title">最近搜索：</span>
            <a herf="javascript:;" @click="clear" v-if="$store.state.hList.length > 0">清空历史</a>
        </div>
        <div class="h-list">
            <span class="label" v-for="item in $store.state.hList" :key="item" @click="search(item)">{{item}}</span>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'search',
    mixins: [http],
    watch: {
        '$route'() {
            this.init();

        }
    },
    data() {
        return {
            list: []
        }
    },
    methods: {
        clear() {
            window.localStorage.removeItem('__SearchHistory__');

            this.sethList([])
        },
        init() {
            this.getList()

            setTimeout(function() {
                document.body.scrollTop = 0;
            }, 500 )
        },
        getList() {
            let _list = window.localStorage.getItem('__SearchHistory__');
            if (_list) {
                _list = JSON.parse(_list);
                this.sethList(_list)
            }
        },
        search() {

        }

    },
    mounted() {
        this.init();


    }


}

</script>
