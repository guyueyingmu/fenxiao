<template>
    <div v-loading="loading">
        <div class="ui-tags">

            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">退款记录</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">换货记录</span>

        </div>
        <ul class="order-list" v-show="list.length > 0" v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10">
            <li v-for="item in list" :key="item.id">
                <div class="h">订单号：{{item.order_number}}</div>
                <div class="m" v-for="good in item.orders_goods" :key="good.id">
                    <img :src="good.good_img" width="50" height="50">
                    <div class="info">
                        <div class="title">{{good.good_title}}</div>
                    </div>
                    <div class="tool">
                        <div v-if="item.pay_method == 3">积分 {{good.credits}}</div>
                        <div v-else>￥{{good.price}}</div>
                        <div>x {{good.buy_num}}</div>
                    </div>
                </div>
                <div class="b">
                    <!-- <div>订单总额:￥{{item.total_amount}}</div> -->
                    <div>
                        售后进度：
                        <span class="status">{{status(item.status)}}</span>
                    </div>
                </div>
            </li>

        </ul>

        <div class="nodata" v-if="list.length < 1 && loading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有售后记录~</div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'shouhou',
    mixins: [http],
    data() {
        return {
            refund_list: [],
            exchange_list: [],
            tagsIdx: 1,
            sloading: false,
            r_pages: {},
            e_pages: {}

        }
    },
    computed: {
        list() {
            if (this.tagsIdx == 1) {
                return this.refund_list
            } else {
                return this.exchange_list
            }
        },

    },
    methods: {
        loadMore() {
            if (this.sloading) { return }
            if (this.tagsIdx == 1) {
                let page = parseInt(this.r_pages.current_page, 10) || 1;
                
                if (page < this.r_pages.total_page) {
                    this.get_list(page + 1);
                }
            } else {
                let page = parseInt(this.e_pages.current_page, 10) || 1;
                if (page < this.e_pages.total_page) {
                    this.get_list(page + 1);
                }
              
            }


        },
        status(idx) {
            let r = ['未处理', '已同意', '已拒绝']
            let _idx = parseInt(idx, 10)
            return r[_idx - 1]
        },
        //换货
        exchange(page) {
            let url = '/mini/Exchange/get_list?page=' + (page || 1), vm = this;
            vm.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.exchange_list = res.data.list;
                    vm.e_pages = res.data.pages
                } else {
                    vm.handleError(res)
                }
            })

        },
        //退款
        refund(page) {
            let url = '/mini/Refund/get_list?page=' + (page || 1), vm = this;

            vm.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.refund_list = res.data.list;
                    vm.r_pages = res.data.pages
                } else {
                    vm.handleError(res)
                }
            })
        },
        get_list(page) {
            if (this.tagsIdx == 1) {
                this.refund(page)

            } else {
                this.exchange(page)
            }
        }
    },
    created() {
        this.setTitle('售后记录')
        this.refund();
        this.exchange();

    }
}
</script>
