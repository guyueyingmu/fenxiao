<template>
    <div>
        <ul class="ui-links">
            <li v-for="(cat, k) in cat_list" :key="k" @click="goto('/search/cat_id/'+cat.id)">
                <img :src="cat.cat_img" width="50" height="50">
                <span class="title">{{cat.cat_name}}</span>
                <i class="iconfont icon-arrow"></i>
            </li>
        </ul>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'class',
    mixins: [http],
    data() {
        return {            
            cat_list: [],
        }
    },
    methods: {
        get_cat() {
            let url = '/mini/Home/get_cat_list?page=1',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.cat_list = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    mounted() {
        this.setTitle('商品分类')

        this.get_cat();
    }


}

</script>
