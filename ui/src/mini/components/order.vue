<template>
    <div v-loading="loading">
        <div class="ui-tags">
            <span :class="{'active':tagsIdx == 0}" @click="tagsIdx = 0">全部</span>
            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">待付款</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">待收货</span>
            <span :class="{'active':tagsIdx == 3}" @click="tagsIdx = 3">已完成</span>
            <span :class="{'active':tagsIdx == 4}" @click="tagsIdx = 4">已取消</span>

        </div>
        <ul class="order-list">
            <li>
                <div class="h">订单号：162208000112
                    <span class="status">已发货</span>
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
                <div class="m">
                    <img src="" width="50" height="50">
                    <div class="info">
                        <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠美</div>
                    </div>
                    <div class="tool">
                        <div>￥198.00</div>
                        <div>x 1</div>
                    </div>
                </div>
                <div class="b">
                    <div>订单总额:￥198.00</div>
                    <div>
                        <button type="button" class="ui-btn">退款</button>
                        <button type="button" class="ui-btn active">换货</button>
                    </div>
                </div>
            </li>
             <li>
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
                        <!-- <button type="button" class="ui-btn active">付款</button> -->
                    </div>
                </div>
            </li>
        </ul>

        <div class="nodata" v-if="list.length < 1 && loading == false">
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
            tagsIdx: 0
        }
    },
    methods: {
        ondel() {

        },
        get_list() {
        }
    },
    created() {
        this.setTitle('我的订单')
        // this.get_list();

    }
}
</script>
