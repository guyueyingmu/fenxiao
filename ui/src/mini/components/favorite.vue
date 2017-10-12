<template>
    <div v-loading="loading">
        <ul class="thumb-list" v-if="loading == false && list.length > 0" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" :infinite-scroll-immediate-check="false" infinite-scroll-distance="10">
            <li v-for="(item,idx) in list" :key="idx">
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
                timer: null
            }
        },
        methods: {
            loadMore() {
                let page = parseInt(this.pages.current_page, 10);
                clearTimeout(this.timer)
                this.timer = setTimeout(() => {
                    if (page < this.pages.total_page) {
                        this.get_list(page + 1);
                    }
                }, 500)


            },
            get_list(page) {
                page = page || 1;       
                let url = '/mini/Collect/get_list?page='+page,
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
                 
                })
            },
            //删除
            ondel(index) {
                let url = '/mini/Collect/del', vm = this, data = { good_id: this.list[index].good_id };
                this.apiPost(url, data).then(function(res) {
                    if (res.code) {
                        vm.$msg(res.msg);
                        vm.list.splice(index,1);
                    } else {
                        vm.handleError(res)
                    }
                })
            },
        },
        created() {
            this.setTitle('我的收藏')
            this.get_list();
        }
    }
</script>
