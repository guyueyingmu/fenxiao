<template>
    <div class="paySuccess">
        <i class="iconfont icon-xuanze"></i>
        <h1 v-if="info.pay_method == 1">支付成功</h1>
        <h1 v-if="info.pay_method == 2">该商品为线下服务商品，请等待客服人员与您联系！</h1>
        <h1 v-if="info.pay_method == 3">积分兑换成功</h1>
       <div class="other">
             <div>订单号：{{info.order_number}}</div>
               <div class="space-a"></div>
        <button type="button" class="ui-btn" @click="goto('/order_detail/order_id/'+info.id)">查看订单</button>
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
            info: {},
            order_id: 0,
        }
    },
    methods: {
        //获取订单信息
        get_order_info(){
            let url = '/mini/Order/detail',
                vm = this;

            this.apiGet(url, {order_id: this.order_id}).then(function(res) {
                if (res.code) {
                    vm.info = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('支付成功');
        this.order_id = this.$route.params.order_id;
        this.get_order_info();
    }


}

</script>
