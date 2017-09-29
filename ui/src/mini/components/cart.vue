<template>
    <div class="cart">
        <ul class="thumb-list">
            <li v-for="(item,idx) in list" :key="item.id">
                <input type="checkbox" class="ui-checkbox" v-model="item.selected" @change="onChange">
                <img :src="item.thumb" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.title}}</div>
                    <div class="tool">
                        <span class="price">￥
                            <em>{{item.price}}</em>
                        </span>
                        <span class="totle">
                            <input-num v-model="item.total"></input-num>
                        </span>
                    </div>
                </div>
                <i class="iconfont icon-shanchu "></i>
            </li>
        </ul>
        <div class="nodata">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您的购物车是空的~</div>
        </div>
        <div class="tool-bottom">
            <div><input type="checkbox" id="allcheackbox" v-model="allCheakbox" class="ui-checkbox" @change="onAllCheack"></div>
            <div>
                <label for="allcheackbox">全选</label>
            </div>
            <div class="total_label">合计:</div>
            <div class="total">
                <span>￥
                    <em>{{total.toFixed(2)}}</em>
                </span>
            </div>
            <button type="button" class="ui-jiesuan">去结算</button>
        </div>
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
            list: [
                {
                    id: 1,
                    thumb: 'static/mini/img/demo/3.png',
                    title: 'SK-II"神仙水"晶透修护礼盒（神仙水230ml+洁面霜20g+清莹露30ml+微肌因修护精华霜15g）（护肤套装）',
                    price: '390.99',
                    total: 1,
                    selected: false
                },
                {
                    id: 2,
                    thumb: 'static/mini/img/demo/1.png',
                    title: '兰蔻（LANCOME）新精华肌底液30ml（小黑瓶 精华液 补水保湿提拉紧致 新老品随机发货）',
                    price: '649.00',
                    total: 1,
                    selected: false
                },
            ]
            ,
            allCheakbox: false
        }
    },
    computed: {
        total() {
            let _d = this.list;
            let n = 0, i = 0;
            for (let item of _d) {
                if (item.selected) {
                    n = n + parseFloat(item.price) * parseInt(item.total, 10);
                    i = i + 1;
                }
            }
            if (i == _d.length) {
                this.allCheakbox = true;
            } else {
                this.allCheakbox = false;
            }

            return n

        }
    },
    methods: {
        onChange() {

        },
        onAllCheack(event) {
            let _d = this.list;
            for (let item of _d) {
                item.selected = event.target.checked;
            }
        }

    },
    mounted() {
        this.setTitle('我的购物车')

    },
}

</script>
