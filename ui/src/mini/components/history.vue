<template>
    <div v-loading="loading">
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
        ondel(idx) {

            this.list.splice(idx, 1);
            window.localStorage.setItem("__history__", JSON.stringify(this.list));
        },
        get_list() {
            this.loading = true
            let _a = window.localStorage.getItem("__history__");
            if (_a) {

                this.loading = false
                this.list = JSON.parse(_a)
            }
        }
    },
    created() {
        this.setTitle('我的足迹')
        this.get_list();

    }
}
</script>
