<template>
    <div>
        <img src="static/mini/img/code.png"  style="width:100%;">
       <div style="padding:13px;">
           <a herf="javascript:;" class="ui-btn ui-btn-block" @click="save_data()">我要代理</a>
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
        save_data(){
            let url = '/mini/Home/dis_apply', vm = this, data = { };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.good_info.is_collect = 0;
                    vm.$msg(res.msg);
                } else {
                    if(res.url){
                        vm.goto(res.url);
                    }
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('我要代理')
     
    }
}
</script>
