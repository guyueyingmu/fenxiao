<template>
    <div class="order">
        <ul class="ui-links">
            <li  @click="goto('/address')">
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
                <i class="iconfont icon-arrow"></i>
            </li>
              <li class="noactive">
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
            </li>
            <li class="noAdd" @click="goto('/address')">
                <div class="title">
                    <div>
                        <i class="iconfont icon-tanhao"></i>
                    </div>
                    <div> 还没有添加收货人信息~ 点击添加</div>
                </div>
            </li>

        </ul>
        <div style="height:10px;"></div>
        <ul class="thumb-list l-t">
            <li v-for="(item,idx) in list" :key="item.id">
                <img :src="item.thumb" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.title}}</div>
                    <div class="tool">
                        <span class="grey">x {{item.total}}</span>
                        <span class="red">￥<em>{{item.price}}</em></span>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="ui-fixd">
            <li>
                <div class="m">商品总计</div>
                <div class="r">￥396</div>
            </li>
              <li>
                <div class="m">应付总额</div>
                <div class="r"><span class="price">￥<em>396</em></span></div>
            </li>
        </ul>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2">提交</button>
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
            list: []
        }
    },
    created() {
        this.setTitle('确认订单')
        let _list = this.$store.state.cart;
        this.list = _list;

    }


}

</script>
