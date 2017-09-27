<template>
    <div>
        <ul class="ui-links">
            <li>
                <img src="static/app/demo/1.png" width="50" height="50">
                <span class="title">美白系列</span>
                <i class="iconfont icon-arrow"></i>
            </li>
            <li>
                <img src="static/app/demo/1.png" width="50" height="50">
                <span class="title">美白系列2</span>
                <i class="iconfont icon-arrow"></i>
            </li>
            <li>
                <img src="static/app/demo/1.png" width="50" height="50">
                <span class="title">美白系列3</span>
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
    mounted() {
        this.setTitle('商品分类')

    }


}

</script>
