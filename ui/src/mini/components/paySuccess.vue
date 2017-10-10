<template>
    <div class="paySuccess">
        <i class="iconfont icon-xuanze"></i>
        <h1>支付成功</h1>
       <div class="other">
             <div>订单号：185454544545</div>
               <div class="space-a"></div>
        <button type="button" class="ui-btn">查看订单</button>
       </div>
        
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'class',
    mixins: [http],
    created() {
        this.setTitle('支付成功')

    }


}

</script>
