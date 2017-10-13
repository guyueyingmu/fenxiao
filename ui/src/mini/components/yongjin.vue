<template>
    <div>
        <ul class="ui-fixd"  v-show="list.length > 0"  v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
            <li class="med" v-for="(item,idx) in list" :key="idx">
                <div class="f">
                    <div>时间：2017-8-9 15:15:10</div>
                    <div>级别：一级获佣</div>
                </div>
                <div class="red">+ ￥6.9</div>
              
            </li>
        </ul>
        <div class="spinner" v-if="sloading">
            <mt-spinner  :size="18" color="#26a2ff"></mt-spinner>
        </div>
        <div class="nodata-line" v-else-if="pages.total_page == pages.current_page">没有更多数据了</div>

        <div class="nodata" v-if="list.length < 1 && sloading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有拥金记录~</div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'yongjin',
    mixins: [http],
    data() {
        return {
            list: [],
            pages: {},
            sloading: false
        }
    },
    methods: {
   
        loadMore() {
         
            if (this.sloading) { return }
            let page = parseInt(this.pages.current_page, 10) || 1;
            if (page < this.pages.total_page) {
                this.get_list(page + 1);
            }

        },
        get_list(page) {
            page = page || 1;
            let url = '/mini/Collect/get_list?page=' + page,
                vm = this;
            vm.sloading = true
            this.apiGet(url).then(function(res) {

                if (res.code) {
                   
                    vm.pages = res.data.pages;
                    if (vm.list.length == 0) {
                        vm.list = res.data.list;


                    } else {
                        let _list = vm.list;
                        vm.list = _list.concat(res.data.list)


                    }
                    setTimeout(() => {
                        vm.sloading = false;
                         vm.$emit('cheackLoadMore')
                    }, 200)



                } else {
                    vm.handleError(res)
                }

            })
        },
    },
    created() {
        this.setTitle('拥金记录')
        this.get_list();
    },

}
</script>
