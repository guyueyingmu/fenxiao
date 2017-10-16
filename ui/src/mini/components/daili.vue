<template>
    <div>
        <img src="/static/mini/img/code.png"  style="width:100%;">
       <div style="padding:13px;">
           <a herf="javascript:;" class="ui-btn ui-btn-block" v-if="applied == 0" @click="save_data()">我要代理</a>
       </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            applied: 0
        }
    },
    methods: {
        check_apply(){
            let url = '/mini/Home/dis_applly_check', vm = this, data = { };
            this.apiGet(url, data).then(function(res) {
                if (res.code) {
                    if(res.data > 0){
                        vm.applied = 1;
                    }
                } else {
                    vm.handleError(res)
                }
            })
        },
        save_data(){
            let url = '/mini/Home/dis_apply', vm = this, data = { };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('我要代理');
        this.check_apply();
    }
}
</script>
