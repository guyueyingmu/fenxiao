<template>
    <div>
        <div class="ui-cart">
            <div>订单状态：<span class="orange">待处理</span> </div>
             <div>订单编号：162208000112</div>
            <div>下单时间：162208000112</div>
            <div>支付状态：未支付</div>
            <div>订单总额：￥198</div>
           
        </div>
        <div class="space15"></div>
        <ul class="thumb-list" >
            <li>
                <img src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    <div class="tool">
                        <div>x 1</div>
                        <div>￥950.00</div>
                    </div>
                </div>
            </li>
              <li>
                <img src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    <div class="tool">
                        <div>x 1</div>
                        <div>￥950.00</div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="rate-warp">
             <rate :value="stars" :readonly="true" :text="true"></rate>
             <div>
                 <textarea class="ui-input"></textarea>                 
             </div>
        </div>

        <div class="nodata" v-if="list.length < 1 && loading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有访问记录~</div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
import Rate from './rate'
export default {
    name: 'favorite',
    mixins: [http],
    components: {
        Rate
    },
    data() {
        return {
            list: [],
            stars:0
        }
    },
    methods: {
      
    },
    created() {
        this.setTitle('订单详情')
       // this.get_list();

    }
}
</script>
