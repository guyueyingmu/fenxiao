<template>
    <div class="add-address-box">
        <div class="tips">
            <i class="iconfont icon-tanhao"></i> 请输入有效的地址信息，以便能正确收到货品</div>
        <div class="ui-input-list">
            <li>
                <div class="m">收货人：</div>
                <div class="f"><input type="text" maxlength="4" class="ui-input" placeholder="请输入姓名"></div>
            </li>

            <li>
                <div class="m">手机号：</div>
                <div class="f"><input type="tel" maxlength="11" class="ui-input" placeholder="请输入手机号"></div>
            </li>

            <li>
                <div class="m">配送至：</div>
                <div class="f">
                    <div>
                        <ui-area v-model="area"></ui-area>

                    </div>
                    <div><input type="text" class="ui-input" placeholder="请输入详细地址"></div>
                </div>
            </li>

        </div>
        <div class="space"></div>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2">保存</button>
            </div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
import uiArea from './area'
export default {
    name: 'class',
    mixins: [http],
    components: {
        uiArea
    },
    data() {
        return {
            area: {
                
                province:'广东',
                city:'广州',
                area:''
            }
        }
    },
    mounted() {
        this.setTitle('添加地址')

    }


}

</script>
