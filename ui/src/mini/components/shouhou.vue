<template>
    <div v-loading="loading">
        <div class="ui-tags">

            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">退款记录</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">换货记录</span>

        </div>
        <ul class="order-list">
            <li>
                <div class="h">订单号：162208000112
       
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
                        售后进度：<span class="status">已同意</span>
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
    name: 'favorite',
    mixins: [http],
    data() {
        return {
            list: [],
            tagsIdx: 1
        }
    },
    methods: {
        ondel() {

        },
        get_list() {
        }
    },
    created() {
        this.setTitle('售后记录')
        // this.get_list();

    }
}
</script>
