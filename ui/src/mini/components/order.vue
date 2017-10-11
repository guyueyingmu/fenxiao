<template>
    <div v-loading="loading">
        <div class="ui-tags">
            <span :class="{'active':tagsIdx == 0}" @click="tagsIdx = 0">全部</span>
            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">待付款</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">待收货</span>
            <span :class="{'active':tagsIdx == 3}" @click="tagsIdx = 3">已完成</span>
            <span :class="{'active':tagsIdx == 4}" @click="tagsIdx = 4">已取消</span>

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
                        <div>x {{good.good_num}}</div>
                    </div>
                </div>
                <div class="b">
                    <div>订单总额:￥{{item.total_amount}}</div>
                    <div>
                        <button type="button" class="ui-btn active" v-if="item.pay_status == 1">付款</button>
                        <button type="button" class="ui-btn active" v-if="item.order_status == 5">评价</button>
                        <button type="button" class="ui-btn active" v-if="item.order">退款</button>
                        <button type="button" class="ui-btn active" v-if="item.order_status == 2">换货</button>
                    </div>
                </div>
            </li>
             <!-- <li>
                <div class="h">订单号：162208000112
                    <span class="status">已完成</span>
                </div>
                <div class="m">
                    <img src="" width="50" height="50">
                    <div class="info">
                        <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    </div>
                    <div class="tool">
                        <div>￥198.00</div>
                        <div>x 1</div>
                    </div>
                </div>
                
                <div class="b">
                    <div>订单总额:￥198.00</div>
                    <div>
                        <button type="button" class="ui-btn active">评价</button>
                    </div>
                </div>
            </li>
             <li>
                <div class="h">订单号：162208000112
                    <span class="status">待处理</span>
                </div>
                <div class="m">
                    <img src="" width="50" height="50">
                    <div class="info">
                        <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    </div>
                    <div class="tool">
                        <div>￥198.00</div>
                        <div>x 1</div>
                    </div>
                </div>
                
                <div class="b">
                    <div>订单总额:￥198.00</div>
                    <div>
                     
                        <button type="button" class="ui-btn active">付款</button>
                    </div>
                </div>
            </li>
               <li>
                <div class="h">订单号：162208000112
                    <span class="status">已服务</span>
                </div>
                <div class="m">
                    <img src="" width="50" height="50">
                    <div class="info">
                        <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    </div>
                    <div class="tool">
                        <div>积分198</div>
                        <div>x 1</div>
                    </div>
                </div>
                
                <div class="b">
                    <div>订单总额:积分198</div>
                    <div>
                        <button type="button" class="ui-btn active">付款</button>
                    </div>
                </div>
            </li> -->
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
            tagsIdx: 0
        }
    },
    methods: {
        ondel() {

        },
        loadMore() {
            let page = parseInt(this.page_info.current_page, 10);
            clearTimeout(this.timer)
            this.timer = setTimeout(() => {
                if (page < this.page_info.total_page) {
                    this.get_list(page + 1);
                }
            }, 500)


        },
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
