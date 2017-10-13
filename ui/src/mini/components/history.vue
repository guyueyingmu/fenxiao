<template>
    <div v-loading="loading">
        <ul class="thumb-list" v-show="list.length > 0" v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
            <li v-for="(item,idx) in list" :key="item.id">
                <img :src="item.good_img" width="70" height="70" @click="goto('/detail/id/'+item.good_id)">
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

        <div class="nodata" v-if="list.length < 1 && loading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有访问记录~</div>
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

        ondel(index) {
            let url = '/mini/Footmark/del', vm = this, data = { id: this.list[index].id };
            this.$confirm({
                msg: '确定删除此足迹？',
                yes: function() {
                    vm.apiPost(url, data).then(function(res) {
                        if (res.code) {
                            vm.$msg(res.msg);
                            vm.list.splice(index, 1);
                        } else {
                            vm.handleError(res)
                        }
                    })
                }
            })

        },
        loadMore() {
            if (this.sloading) { return }
            let page = parseInt(this.pages.current_page, 10) || 1;
            if (page < this.pages.total_page) {
                this.get_list(page + 1);
            }

        },
        get_list(page) {
            this.sloading = true
            page = page || 1;
            let url = '/mini/Footmark/get_list?page=' + page,
                vm = this;
            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.pages = res.data.pages;
                    if (page < 2) {
                        vm.list = res.data.list;
                    } else {
                        let _list = vm.list;
                        _list = _list.concat(res.data.list)
                        vm.list = _list;
                    }
                } else {
                    vm.handleError(res)
                }
                setTimeout(() => {
                    vm.sloading = false;
                    vm.$emit('checkLoadMore')
                }, 200)

            })
        }
    },
    created() {
        this.setTitle('我的足迹')
        this.get_list();

    }
}
</script>
