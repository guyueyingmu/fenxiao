<template>
    <div class="add-address-box">
        <div class="tips">
            <i class="iconfont icon-tanhao"></i> 请输入有效的地址信息，以便能正确收到货品</div>
        <div class="ui-input-list">
            <li>
                <div class="m">收货人：</div>
                <div class="f"><input type="text" maxlength="4" v-model="save_data.user_name" class="ui-input" placeholder="请输入姓名"></div>
            </li>

            <li>
                <div class="m">手机号：</div>
                <div class="f"><input type="tel" maxlength="11" class="ui-input" v-model="save_data.phone" placeholder="请输入手机号"></div>
            </li>

            <li>
                <div class="m">配送至：</div>
                <div class="f">
                    <div>
                        <ui-area v-model="area"></ui-area>

                    </div>
                    <div><input type="text" class="ui-input" v-model="save_data.address" placeholder="请输入详细地址"></div>
                </div>
            </li>

        </div>
        <div class="space"></div>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2" @click="save()">保存</button>
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
                province:'',
                city:'',
                area:''
            },
            save_data: {

            },
            isEdit: 0
        }
    },
    methods: {
        //保存
        save(){
            let url = this.isEdit == 1 ? '/mini/Address/edit' : '/mini/Address/add', vm = this, data = this.save_data;
            data.province = this.area.province;
            data.city = this.area.city;
            data.area = this.area.area;
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    // vm.$msg(res.msg);
                    // vm.goto('/address');
                    history.go(-1);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //编辑时获取单条数据
        get_info(id) {
            let url = '/mini/Address/detail?id=' + id,
                vm = this;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.save_data = res.data;
                  
                    vm.area = {
                        province : res.data.province,
                        city : res.data.city,
                        area : res.data.area,
                    }
                } else {
                }
            })
        }
    },
    created() {
        this.setTitle('添加地址')

        if (this.$route.name == 'editAddress') {
            this.setTitle('编辑地址')
            this.isEdit = true;
            this.get_info(this.$route.params.id);
        }
    }


}

</script>
