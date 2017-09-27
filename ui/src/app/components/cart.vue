<template>
    <div>
        <ul class="thumb-list">
            <li>
                <i class="iconfont icon-shanchu"></i>
                <input type="checkbox" class="ui-checkbox">
                <img src="static/app/demo/3.png" width="70" height="70">
                <div class="info">
                    <div class="title">SK-II"神仙水"晶透修护礼盒（神仙水230ml+洁面霜20g+清莹露30ml+微肌因修护精华霜15g）（护肤套装）</div>
                    <div class="tool">
                        <span class="price">￥390.00</span>
                        <span>
                            <input-num v-model="n"></input-num>
                        </span>
                    </div>
                </div>
            </li>

            <li>
                <i class="iconfont icon-shanchu"></i>
                <input type="checkbox" class="ui-checkbox">
                <img src="static/app/demo/2.png" width="70" height="70">
                <div class="info">
                    <div class="title">兰蔻（LANCOME）新精华肌底液30ml（小黑瓶 精华液 补水保湿提拉紧致 新老品随机发货）</div>
                    <div class="tool">
                        <span class="price">￥649.00</span>
                        <span>
                            <input-num v-model="n"></input-num>
                        </span>
                    </div>
                </div>
            </li>

        </ul>
    </div>
</template>
<script>
import http from '@/assets/js/http'
import inputNum from './inputNum'
export default {
    name: 'cart',
    mixins: [http],
    components: {
        inputNum
    },
    data() {
        return {
            n: 1
        }
    },
    mounted() {
        this.setTitle('我的购物车')

    },
}

</script>
