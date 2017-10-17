<template>
    <div class="order">
        <ul class="ui-links" v-if="info.show_address== 1">
            <li @click="goto('/address/is_use/1')" v-if="address.id">
                <div class="title add_info">
                    <div class="flex">
                        <span>收货人：</span>
                        <i>{{address.user_name}}</i>
                    </div>
                    <div class="flex">
                        <span>手机号：</span>
                        <i>{{address.phone}}</i>
                    </div>
                    <div class="flex">
                        <span>配送至：</span>
                        <i v-if="address.province == address.city">
                            {{address.city}}市 {{address.area}} {{address.address}}
                        </i>
                        <i v-else>{{address.province}}省 {{address.city}}市 {{address.area}} {{address.address}}</i>
                    </div>
                </div>
                <i class="iconfont icon-arrow"></i>
            </li>
            <li class="noAdd" @click="goto('/address/is_use/1')" v-else>
                <div class="title">
                    <div>
                        <i class="iconfont icon-tanhao"></i>
                    </div>
                    <div> 还没有添加收货人信息~ 点击添加</div>
                </div>
            </li>
        </ul>

        <div style="height:10px;" v-if="address.id"></div>
        <div v-if="info.good_list.length > 0">

            <ul class="thumb-list l-t">
                <li v-for="(item,idx) in info.good_list" :key="item.id">
                    <img :src="item.good_img" width="70" height="70">
                    <div class="info">
                        <div class="title">{{item.good_title}}</div>
                        <div class="tool">
                            <span class="grey">x {{item.good_count}}</span>
                            <span class="red" v-if="item.good_type == 4 || item.good_type == 5">积分
                                <em>{{item.credits}}</em>
                            </span>
                            <span class="red" v-else>￥
                                <em>{{item.price}}</em>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="ui-fixd">
                <li>
                    <div class="m">商品总计</div>
                    <div class="r" v-if="info.good_list[0].good_type == 4 || info.good_list[0].good_type == 5">积分 {{info.total_credits}}</div>
                    <div class="r" v-else>￥{{info.total_amount}}</div>
                </li>
                <li>
                    <div class="m">应付总额</div>
                    <div class="r" v-if="info.good_list[0].good_type == 4 || info.good_list[0].good_type == 5">
                        <span class="price">积分
                            <em>{{info.total_credits}}</em>
                        </span>
                    </div>
                    <div class="r" v-else>
                        <span class="price">￥
                            <em>{{info.total_amount}}</em>
                        </span>
                    </div>
                </li>
            </ul>
            <div class="btn-wrap">
                <div class="btn-fixed">
                    <button type="button" class="ui-btn ui-btn-block ui-btn-l2" @click="save_data()">提交</button>
                </div>
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
            post_good_list:[],
            info: {
                good_list: [],
                address: {},
                show_address: 1,
                total_amount: '',
                total_credits: 1
            },
            address: {}
        }
    },
    methods: {
        get_good_info() {
            let url = '/mini/Order/check_order',
                vm = this;

            this.apiGet(url, this.post_good_list).then(function(res) {
                if (res.code) {
                    vm.info = res.data;
                    if (res.data.show_address == 1) {
                        let _add = window.localStorage.getItem('__Select_Address__')
                        if (_add) {
                            vm.address = JSON.parse(_add)
                        } else {
                            vm.address = {}
                        }
                    }

                } else {
                    vm.handleError(res)
                }
            })
        },
        save_data() {
            if(this.info.show_address && !this.address.id){
            console.log(this.address.id)
                this.$msg('请选择配送地址');
                return false;
            }
            let url = '/mini/Order/add_order', vm = this, data = { good_info: JSON.stringify(vm.post_good_list), address_id: vm.address.id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                    setTimeout(function(){
                        if (res.data.pay_method == 2) {
                            vm.goto('/success/order_id/' + res.data.order_id);
                        } else {
                            vm.goto('/pay/order_id/' + res.data.order_id);
                        }
                    },500);
                    
                    window.localStorage.removeItem('__CART__')
                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('确认订单')
        let _list = this.$store.state.cart;

        if (_list.length < 1) {
            _list = window.localStorage.getItem('__CART__')
            if (_list) {
                _list = JSON.parse(_list)
            }

        }
        this.post_good_list = _list;
        this.get_good_info();
    }


}

</script>
