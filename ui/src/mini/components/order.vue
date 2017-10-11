<template>
    <div v-loading="loading">
        <div class="ui-tags">
            <span :class="{'active':search.keyword == 0}" @click="onSearch(0)">全部</span>
            <span :class="{'active':search.keyword == 1}" @click="onSearch(1)">待付款</span>
            <span :class="{'active':search.keyword == 2}" @click="onSearch(2)">待收货</span>
            <span :class="{'active':search.keyword == 5}" @click="onSearch(5)">已完成</span>
            <span :class="{'active':search.keyword == 4}" @click="onSearch(4)">已取消</span>

        </div>
        <ul class="order-list" v-if="list.length > 0" v-infinite-scroll="loadMore" infinite-scroll-disabled="loadComment" :infinite-scroll-immediate-check="false" infinite-scroll-distance="10">
            <li v-for="(item,idx) in list" :key="idx">
                <div class="h">订单号：{{item.order_number}}
                    <span class="status">{{item.order_status_txt}}</span>
                </div>
                <div class="m" v-for="(good,key) in item.orders_goods" :key="key">
                    <img :src="good.good_img" width="50" height="50">
                    <div class="info">
                        <div class="title">{{good.good_title}}</div>
                    </div>
                    <div class="tool">
                        <div>￥{{good.price}}</div>
                        <div>x {{good.buy_num}}</div>
                    </div>
                </div>
                <div class="b">
                    <div>订单总额:￥{{item.total_amount}}</div>
                    <div>
                        <button type="button" class="ui-btn active" v-if="item.pay_status == 1 && item.pay_method != 2" @click="goto('/pay/order_id/'+item.id)">付款</button>
                        <button type="button" class="ui-btn active" v-if="item.comment_status == 1" @click="goto('/start/order_id/'+item.id)">评价</button>
                        <button type="button" class="ui-btn active" v-if="item.refund == 1" @click="refund(idx)">退款</button>
                        <button type="button" class="ui-btn active" v-if="item.exchange == 1" @click="exchange(idx)">换货</button>
                    </div>
                </div>
            </li>
        </ul>

        <div class="nodata" v-else>
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有订单记录~</div>
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
            page_info: {},
            search: {
                keyword: 0,
            },
        }
    },
    methods: {
        //换货
        exchange(index){
            let url = '/mini/Exchange/add', vm = this, data = { order_id: this.list[index].id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.list[index].exchange = 0;
                    vm.list[index].refund = 0;
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //退款
        refund(index){
            let url = '/mini/Refund/add', vm = this, data = { order_id: this.list[index].id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.list[index].refund = 0;
                    vm.list[index].exchange = 0;
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //加载更多
        loadMore() {
            let page = parseInt(this.page_info.current_page, 10);
            clearTimeout(this.timer)
            this.timer = setTimeout(() => {
                if (page < this.page_info.total_page) {
                    this.get_list(page + 1);
                }
            }, 500)


        },
        //搜索
        onSearch(keyword) {
            this.search.keyword = keyword;
            let searchData = { status: this.search.keyword }
            this.get_list(1, searchData)
        },
        //获取数据
        get_list(page, searchData) {
            page = page || 1;
            let url = '/mini/Order/get_list?page=' + page,
                vm = this;

            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.page_info = res.data.pages;
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
        }
    },
    created() {
        this.setTitle('我的订单')
        this.get_list();
    }
}
</script>
