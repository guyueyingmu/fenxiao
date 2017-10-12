<template>
    <div v-loading="loading">
        <div class="ui-tags">

            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">退款记录</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">换货记录</span>

        </div>
        <ul class="order-list">
            <li v-for="item in list" :key="item.id">
                <div class="h">订单号：{{item.order_number}}</div>
                <div class="m" v-for="good in item.orders_goods" :key="good.id">
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
                    <div>订单总额:￥</div>
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
        status(idx) {
            let r = ['未处理', '同意', '拒绝']
            let _idx = parseInt(idx, 10)
            return r[_idx - 1]
        },
        //换货
        exchange() {
            let url = '/mini/Exchange/get_list', vm = this;
            vm.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.exchange_list = res.data.list;
                } else {
                    vm.handleError(res)
                }
            })

        },
        //退款
        refund() {
            let url = '/mini/Refund/get_list', vm = this;

            vm.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.refund_list = res.data.list;
                } else {
                    vm.handleError(res)
                }
            })


        },
    },
    created() {
        this.setTitle('售后记录')
        this.refund();
        this.exchange();

    }
}
</script>
