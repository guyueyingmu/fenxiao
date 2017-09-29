<template>
    <div class="address">
        <ul class="ui-links">
            <li>
                <div style="margin-right:10px;"><input type="checkbox" class="ui-checkbox"> </div>
                <div class="info">
                    <div class="title">
                        <div class="flex">
                            <span>收货人：</span>
                            <i>李四</i>
                        </div>
                        <div class="flex">
                            <span>手机号：</span>
                            <i>18520128255</i>
                        </div>
                        <div class="flex">
                            <span>配送至：</span>
                            <i>广州市海珠区丽影广场a座801广州市海珠区丽影广场a座801</i>
                        </div>
                    </div>
                    <div class="tool">
                        <span class="ui-btn ui-btn-setDef">设为默认</span>
                        <span class="ui-btn ui-btn-edit">
                            <i class="iconfont icon-bianji"></i> 编辑</span>

                    </div>
                </div>
                <i class="iconfont icon-shanchu "></i>
            </li>
            <li>
                <div style="margin-right:10px;"><input type="checkbox" class="ui-checkbox" checked> </div>
                <div class="info">
                      <div class="title">
                        <div class="flex">
                            <span>收货人：</span>
                            <i>李四</i>
                        </div>
                        <div class="flex">
                            <span>手机号：</span>
                            <i>18520128255</i>
                        </div>
                        <div class="flex">
                            <span>配送至：</span>
                            <i>广州市海珠区丽影广场a座801广州市海珠区丽影广场a座801</i>
                        </div>
                    </div>
                    <div class="tool">
                        <span class="skin">默认地址</span>
                        <span class="ui-btn ui-btn-edit">
                            <i class="iconfont icon-bianji"></i> 编辑</span>

                    </div>
                </div>
                <i class="iconfont icon-shanchu "></i>
            </li>
        </ul>

         <div class="nodata">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>没有找到数据</div>
        </div>
        <div class="add_address" @click="goto('/add_address')">
            <i class="iconfont icon-jia"></i> 添加地址
        </div>
        <div class="space"></div>
           <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2">选择并使用</button>
            </div>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'class',
    mixins: [http],
    mounted() {
        this.setTitle('选择地址')

    }


}

</script>
