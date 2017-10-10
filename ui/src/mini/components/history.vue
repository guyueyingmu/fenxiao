<template>
    <div v-loading="loading">
        <ul class="thumb-list" v-if="loading == false && list.length > 0">
            <li>
                <img src="" width="70" height="70" @click="goto('/detail')">
                <div class="info" style="margin-right:1.5em" @click="goto('/detail')">
                    <div class="title">美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠美白系列美白霜好的美白霜，特价优惠</div>
                    <div class="tool">
                        <span class="price">￥
                            <em>500.00</em>
                        </span>
                    </div>
                </div>
                <i class="iconfont icon-shanchu " @click="ondel(idx);"></i>
            </li>
        </ul>

        <div class="nodata" v-if="list.length < 1 && loading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有访问记录~</div>
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
        }
    },
    methods: {
        ondel() {
        },
        get_list(){
            let _a = window.localStorage.getItem("__history__");
            if(_a){
                this.list = JSON.parse(a)
            }
        }
    },
    created() {
        this.setTitle('我的足迹')


    }
}
</script>
