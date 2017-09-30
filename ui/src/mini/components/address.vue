<template>
    <div class="address">
        <ul class="ui-links">
            <li v-for="(item, idx) in list" :key="idx">
                <div style="margin-right:10px;"><input type="checkbox" class="ui-checkbox"> </div>
                <div class="info">
                    <div class="title">
                        <div class="flex">
                            <span>收货人：</span>
                            <i>{{item.user_name}}</i>
                        </div>
                        <div class="flex">
                            <span>手机号：</span>
                            <i>{{item.phone}}</i>
                        </div>
                        <div class="flex">
                            <span>配送至：</span>
                            <i>{{item.province}}{{item.city}}{{item.area}}{{item.area}}</i>
                        </div>
                    </div>
                    <div class="tool">
                        <span class="skin" v-if="item.is_default == 1">默认地址</span>
                        <span class="ui-btn ui-btn-setDef" v-else>设为默认</span>
                        <span class="ui-btn ui-btn-edit">
                            <i class="iconfont icon-bianji"></i> 编辑</span>

                    </div>
                </div>
                <i class="iconfont icon-shanchu "></i>
            </li>
        </ul>

        <div class="nodata" v-if="list.length < 1">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>没有找到数据</div>
        </div>
        <div class="add_address" @click="goto('/add_address')">
            <i class="iconfont icon-jia"></i> 添加地址
        </div>
        <div class="space"></div>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2">选择并使用</button>
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
        }
    },
    methods: {
        ondel(index){
            this.del(index);
        },
        //删除
        del(k){
            let url = '/mini/Cart/del', vm = this, data = {good_id: this.list[k].good_id};
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    // vm.$msg.success(res.msg);
                    vm.list.splice(k,1);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //取数据
        get_list(page) {
            let url = '/mini/Address/get_list',
                vm = this;

            vm.loading = true;
            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.list = res.data;
                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },

    },
    mounted() {
        this.setTitle('选择地址')
        this.get_list();
    }


}

</script>
