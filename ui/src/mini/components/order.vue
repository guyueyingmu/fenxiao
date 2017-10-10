<template>
    <div v-loading="loading">
        <div class="ui-tags">
            <span :class="{'active':tagsIdx == 0}" @click="tagsIdx = 0">全部</span>
            <span :class="{'active':tagsIdx == 1}" @click="tagsIdx = 1">待付款</span>
            <span :class="{'active':tagsIdx == 2}" @click="tagsIdx = 2">待收货</span>
            <span :class="{'active':tagsIdx == 3}" @click="tagsIdx = 3">已完成</span>
            <span :class="{'active':tagsIdx == 4}" @click="tagsIdx = 4">已取消</span>

        </div>
        <ul class="thumb-list" v-if="loading == false && list.length > 0">

            <li v-for="(item,idx) in list" :key="item.id">
                <img :src="item.good_img" width="70" height="70" @click="goto('/detail/id/'+item.id)">
                <div class="info" style="margin-right:1.5em" @click="goto('/detail/id/'+item.id)">
                    <div class="title">{{item.good_title}}</div>
                    <div class="tool">
                        <span class="price">￥
                            <em>{{item.price}}</em>
                        </span>
                    </div>
                </div>
                <i class="iconfont icon-shanchu " @click="ondel(idx);"></i>
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
            tagsIdx:0
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
