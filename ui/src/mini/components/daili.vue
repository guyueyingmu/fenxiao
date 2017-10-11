<template>
    <div>
        <img src="static/mini/img/code.png"  style="width:100%;">
       <div style="padding:13px;">
           <a herf="javascript:;" class="ui-btn ui-btn-block">我要代理</a>
       </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
          
        }
    },
    methods: {
     
    },
    created() {
        this.setTitle('我要代理')
     
    }
}
</script>
