<template>
    <div class="order">
        <ul class="ui-links">
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
                <!-- <i class="iconfont icon-arrow"></i> -->
            </li>
              <!-- <li class="noactive">
                <div class="title add_info">
                    <div class="flex">
                        <span>收货人：</span>
                        <i>李四</i>
                    </div>
                    <div class="flex">
                        <span>手机号：</span>
                        <i>18520128255</i>
                    </div>
                    <div class="flex">
                        <span>配送至：</span>
                        <i>广东省 广州市 海珠区 丽影广场a座801 丽影广场a座801</i>
                    </div>
                </div>
            </li> -->
            <!-- <li class="noAdd" @click="goto('/address/is_use/1')" v-else>
                <div class="title">
                    <div>
                        <i class="iconfont icon-tanhao"></i>
                    </div>
                    <div> 还没有添加收货人信息~ 点击添加</div>
                </div>
            </li> -->

        </ul>
        <div style="height:10px;"></div>
        <ul class="thumb-list l-t">
            <li v-for="(item,idx) in list.orders_goods" :key="item.id">
                <img :src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <span class="grey">x {{item.buy_num}}</span>
                        <span class="red">￥<em>{{item.price}}</em></span>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="ui-fixd">
            <li>
                <div class="m">商品总计</div>
                <div class="r">￥{{list.total_amount}}</div>
            </li>
              <li>
                <div class="m">应付总额</div>
                <div class="r"><span class="price">￥<em>{{list.total_amount}}</em></span></div>
            </li>
        </ul>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2">确定支付</button>
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
            list: [],
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
        //获取支付信息
        get_pay_info(){
            let url = '/mini/Payment/index',
                vm = this;

            this.apiGet(url, {order_id: this.order_id}).then(function(res) {
                if (res.code) {
                    vm.jsApiParameters = res.data.jsApiParameters;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('支付订单')
        this.order_id = this.$route.params.order_id;
        this.get_order_info();
    }


}

</script>
