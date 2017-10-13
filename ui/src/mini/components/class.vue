<template>
    <div>
        
        <div id="scrollTouch">
            <div class="scroll-tip-w">
                <mt-spinner :size="18" color="#26a2ff"></mt-spinner><span id="scroll-tip"></span>
            </div>
            <ul class="ui-links">
                <li v-for="(cat, k) in cat_list" :key="k" @click="goto('/search/cat_id/'+cat.id)">
                    <img v-lazy="cat.cat_img" width="50" height="50">
                    <span class="title">{{cat.cat_name}}</span>
                    <i class="iconfont icon-arrow"></i>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
import reload from '@/assets/js/reload'
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
                    reload()
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('商品分类')

        this.get_cat();
    }


}

</script>
