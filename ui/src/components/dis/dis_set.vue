<template>
    <div style="padding:5% 10% 0 0; " v-loading="loading">
        <el-form ref="ruleForm" label-width="230px" class="demo-ruleForm">
            <el-form-item :label="item.show_name" :prop="item.c_name" v-for="(item,k) in ruleForm" :key="item.id">
                <el-input v-model="item.c_value" :disabled="k == 0 && c_type == 1"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">保存设置</el-button>
            </el-form-item>

        </el-form>
    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            ruleForm: [],
            c_type:'',
        }
    },
    methods: {
        submitForm() {
            this.save_set(this.ruleForm)
        },
        //存数据
        save_set(data) {
           let _data = {data:JSON.stringify(data)}

            let url = '/admin/setting/set',
                vm = this;
            vm.loading = true;
            this.apiPost(url, _data).then(function(res) {
                if (res.code) {
                    console.log(res)
                    vm.$message({
                        type: 'success',
                        message: res.msg
                    });

                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },
        //取数据
        get_set(c_type, searchData) {
            c_type = c_type || ''
            let url = '/admin/setting/get_set?c_type=' + c_type,
                vm = this;

            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.ruleForm = res.data;

                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },
        initData() {
            this.c_type = this.$route.params.c_type;
            let bread = ['分销', '分销设置'];
            if(this.c_type == 2){
                bread = ['用户', '积分设置'];
            }

            this.get_set(this.c_type);
            this.setBreadcrumb(bread)
            this.setMenu('3-4');
        }
    },
    watch: {
        $route: "initData"
    },
    //组件初始化
    created() {
        this.initData();
    }

}
</script>
