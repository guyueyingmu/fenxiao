<template>
    <div class="order">
        <ul class="ui-links">
            <li  @click="goto('/address/is_use/1')" v-if="list.address">
                <div class="title add_info">
                    <div class="flex">
                        <span>收货人：</span>
                        <i>{{list.address.user_name}}</i>
                    </div>
                    <div class="flex">
                        <span>手机号：</span>
                        <i>{{list.address.phone}}</i>
                    </div>
                    <div class="flex">
                        <span>配送至：</span>
                        <i v-if="list.address.province == list.address.city">
                            {{list.address.city}}市 {{list.address.area}} {{list.address.address}}
                        </i>
                        <i v-else>{{list.address.province}}省 {{list.address.city}}市 {{list.address.area}} {{list.address.address}}</i>
                    </div>
                </div>
                <i class="iconfont icon-arrow"></i>
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
            <li class="noAdd" @click="goto('/address/is_use/1')" v-else>
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
            <li v-for="(item,idx) in list.good_list" :key="item.id">
                <img :src="item.good_img" width="70" height="70">
                <div class="info">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <span class="grey">x {{item.good_count}}</span>
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
            list: [],
            good_list: [],
        }
    },
    methods: {
        get_good_info(){
            let url = '/mini/Order/check_order',
                vm = this;

            this.apiGet(url, this.good_list).then(function(res) {
                if (res.code) {
                    vm.list = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('确认订单')
        let _list = this.$store.state.cart;
        this.good_list = _list;
        this.get_good_info();
    }


}

</script>
