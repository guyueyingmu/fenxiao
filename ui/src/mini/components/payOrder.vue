<template>
    <div class="order">
        <ul class="ui-links" v-if="list.consignee_name">
            <li>
                <div class="title add_info">
                    <div class="flex">
                        <span>收货人：</span>
                        <i>{{list.consignee_name}}</i>
                    </div>
                    <div class="flex">
                        <span>手机号：</span>
                        <i>{{list.consignee_phone}}</i>
                    </div>
                    <div class="flex">
                        <span>配送至：</span>
                        <i>
                            {{list.consignee_address}}
                        </i>
                    </div>
                </div>
            </li>

        </ul>
        <div style="height:10px;" v-if="list.consignee_name"></div>
        <ul class="thumb-list l-t">
            <li v-for="(item,idx) in list.orders_goods" :key="item.id">
                <img :src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <span class="grey">x {{item.buy_num}}</span>
                        <span class="red" v-if="item.good_type == 4 || item.good_type == 5">积分 <em>{{item.credits}}</em></span>
                        <span class="red" v-else>￥<em>{{item.price}}</em></span>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="ui-fixd">
            <li>
                <div class="m">商品总计</div>
                <div class="r" v-if="list.orders_goods[0].good_type == 4 || list.orders_goods[0].good_type == 5">积分 {{list.minus_credits}}</div>
                <div class="r" v-else>￥{{list.total_amount}}</div>
            </li>
              <li>
                <div class="m">应付总额</div>
                <div class="r" v-if="list.orders_goods[0].good_type == 4 || list.orders_goods[0].good_type == 5"><span class="price">积分 <em>{{list.minus_credits}}</em></span></div>
                <div class="r"v-else><span class="price">￥<em>{{list.total_amount}}</em></span></div>
            </li>
        </ul>
        <div class="btn-wrap">
            <div class="btn-fixed" v-if="list.pay_status == 1">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2" @click="do_pay();" v-if="list.pay_method == 1">确定支付</button>
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2" @click="credits();" v-if="list.pay_method == 3">确定兑换</button>
            </div>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'class',
    mixins: [http],
    data() {
        return {
            list: {},
            order_id: 0,
            jsApiParameters: {},
        }
    },
    methods: {
        //获取订单信息
        get_order_info(){
            let url = '/mini/Order/detail',
                vm = this;

            this.apiGet(url, {order_id: this.order_id}).then(function(res) {
                if (res.code) {
                    vm.list = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
        //微信支付
        do_pay(){
            location.href=this.list.pay_url;
        },
        //积分兑换
        credits(){
            let url = '/mini/Order/credits_exchange', vm = this, data = { order_id: this.order_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    // vm.$msg(res.msg);
                    vm.goto('/success/order_id/'+vm.order_id);
                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('支付订单')
        this.order_id = this.$route.params.order_id;
        this.get_order_info();
    }


}

</script>
