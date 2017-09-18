<template>
    <div>
        <div class="page_heade" >
            <el-form :model="form" :rules="rules" label-width="200px">

                <el-form-item label="签到获得积分" prop="signin_credits">
                    <el-input v-model="form.signin_credits" auto-complete="off"></el-input>
                </el-form-item>

                <el-form-item label="绑定手机获得积分" prop="bind_phone_credits">
                    <el-input v-model="form.bind_phone_credits" auto-complete="off"></el-input>
                </el-form-item>

                <el-form-item label="分享商品获得积分" prop="share_good_credits">
                    <el-input v-model="form.share_good_credits" auto-complete="off"></el-input>
                </el-form-item>

                <div style="height:40px;"></div>
                <el-form-item label-width="40%">
                    <el-button type="primary" @click="submitForm('form')">保存</el-button>
                </el-form-item>
            </el-form>

        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            dalogi_loading: false,
            form: {},
            rules: {
                signin_credits: [{ required: true, type:'string', message: '请输入签到获得积分', trigger: 'blur' }],
                bind_phone_credits: [{ required: true, type:'string', message: '请输入绑定手机获得积分', trigger: 'blur' }],
                share_good_credits: [{ required: true, type:'string', message: '请输入分享商品获得积分', trigger: 'blur' }],
            }
        }
    },
    methods: {

        //提交表单
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    let url = '/admin/Setting/set',
                        data = this.form,
                        vm = this;
                    this.apiPost(url, data).then(function(res) {
                        if (res.code) {
                            vm.$message.success(res.msg);
                            setTimeout(function() {
                                vm.$router.push('/goods');
                            }, 500)

                        } else {
                            vm.handleError(res)
                        }

                    })

                } else {
                    this.$message.error('请填写必填项！');
                    return false;
                }
            });
        },
        //取数据
        get_list() {
            let url = '/admin/Setting/get_set?c_type=2',
                vm = this;

            vm.loading = true;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.form = res.data;
                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },

    },
    //组件初始化
    created() {
        this.get_list();
        this.setBreadcrumb(['用户', '积分记录'])
        this.setMenu('2-3');

    }

}
</script>
