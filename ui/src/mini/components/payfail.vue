<template>
    <div class="paySuccess fail">
        <i class="iconfont icon-shibai"></i>
        <h1>支付失败</h1>
        <div class="other">
            <div>支付失败，原因金额不足</div>
            <div>订单号：185454544545</div>
            <div class="space-a"></div>
            <button type="button" class="ui-btn">查看订单</button>
            <button type="button" class="ui-btn">重新支付</button>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'class',
    mixins: [http],
    created() {
        this.setTitle('支付失败')

    }


}

</script>
