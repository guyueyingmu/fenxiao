<template>
    <div>

        <ul class="thumb-list" v-show="list.length > 0" ref="ss" v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
            <li v-for="(item,idx) in list" :key="idx">
                <img v-lazy="item.good_img" width="70" height="70" @click="goto('/detail/id/'+item.good_id)">
                <div class="info" style="margin-right:1.5em" @click="goto('/detail/id/'+item.good_id)">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <span class="price">￥
                            <em>{{item.price}}</em>
                        </span>
                    </div>
                </div>
                <i class="iconfont icon-shanchu " @click="ondel(idx);"></i>
            </li>

        </ul>
     <div class="spinner" v-if="sloading">
            <mt-spinner  :size="18" color="#26a2ff"></mt-spinner>
        </div>
        <div class="nodata-line" v-else-if="pages.total_page == pages.current_page">没有更多数据了</div>

        <div class="nodata" v-if="list.length < 1 && sloading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有收藏记录~</div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'favorite',
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
            console.log('load')
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
        //删除
        ondel(index) {
            let url = '/mini/Collect/del', vm = this, data = { good_id: this.list[index].good_id };
            vm.sloading = true
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                    vm.list.splice(index, 1);
                    setTimeout(() => {
                        vm.sloading = false
                    }, 1000)
                } else {
                    vm.handleError(res)
                }

            })
        },
    },
    created() {
        this.setTitle('我的收藏')
        this.get_list();
    },

}
</script>
