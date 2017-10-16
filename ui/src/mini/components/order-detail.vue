<template>
    <div>
        <div class="ui-cart">
            <div>订单状态：
                <span class="orange">{{list.order_status_txt}}</span>
            </div>
            <div>订单编号：{{list.order_number}}</div>
            <div>下单时间：{{list.add_time}}</div>
            <div>支付状态：{{list.pay_status_txt}}</div>
            <div v-if="list.pay_method == 3">订单总额：积分 {{list.minus_credits}}</div>
            <div>订单总额：￥{{list.total_amount}}</div>

        </div>
        <div class="space15"></div>
        <ul class="thumb-list">
            <li v-for="(item,idx) in list.orders_goods" :key="idx">
                <img :src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <div>x {{item.buy_num}}</div>
                        <div v-if="list.pay_method == 3">积分 {{item.credits}}</div>
                        <div v-else>￥{{item.price}}</div>
                    </div>
                </div>
            </li>
        </ul>

        <!-- <div class="nodata" v-if="list.length < 1 && loading == false">
                <i class="iconfont icon-tongyongmeiyoushuju"></i>
                <div>您还没有访问记录~</div>
            </div> -->
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            list: [],
        }
    },
    methods: {
        get_info(id) {
            let url = '/mini/Order/detail?order_id=' + id,
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.list = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('订单详情')
        this.get_info(this.$route.params.order_id);

    }
}
</script>
