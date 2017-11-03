<template>
    <div>
        <div class="page_heade" @keyup.enter="onSearch()">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户昵称/手机号码">
                    <el-input v-model="formInline.keyword" placeholder="用户昵称/手机号码" style="width:180px"></el-input>
                </el-form-item>

                <el-form-item label="注册时间">
                    <el-date-picker v-model="value7" type="daterange" align="right" placeholder="选择日期范围" @change="fromDate" :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>

                <el-form-item label="状态">
                    <el-select v-model="formInline.status" placeholder="状态" style="width:100px" clearable>
                        <el-option :value="1" label="启用"></el-option>
                        <el-option :value="2" label="禁用"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="积分排序">
                    <el-select v-model="formInline.credits_sort" placeholder="积分排序" style="width:100px" clearable>
                        <el-option :value="1" label="降序"></el-option>
                        <el-option :value="2" label="升序"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="账户余额排序">
                    <el-select v-model="formInline.account_sort" placeholder="账户余额排序" style="width:120px" clearable>
                        <el-option :value="1" label="降序"></el-option>
                        <el-option :value="2" label="升序"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="用户ID" width="80"></el-table-column>
            <el-table-column prop="nickname" label="用户昵称"></el-table-column>
            <el-table-column prop="img_url" label="头像" width="80">
                <template slot-scope="scope">
                    <img style="width:50px;" v-if="scope.row.img_url" :src="scope.row.img_url" />
                </template>
            </el-table-column>
            <el-table-column prop="sex" label="性别" width="60">
                <template slot-scope="scope">
                    {{scope.row.sex == 1?'男':scope.row.sex == 2?'女':'保密'}}
                </template>
            </el-table-column>
            <el-table-column prop="province" label="省份" width="90"></el-table-column>
            <el-table-column prop="city" label="城市" width="80"></el-table-column>
            <el-table-column prop="phone_number" label="手机号码" width="110"></el-table-column>
            <el-table-column prop="credits" label="积分" width="80"></el-table-column>
            <el-table-column prop="account_balance" label="账户余额" width="80"></el-table-column>
            <el-table-column prop="status" label="登录状态" width="80">
                <template slot-scope="scope">
                    {{scope.row.status == 1?'启用':'禁用'}}
                </template>
            </el-table-column>
            <el-table-column prop="register_time" label="注册时间" width="140"></el-table-column>
            <el-table-column prop="last_login_time" label="最近登录时间" width="140"></el-table-column>
            <el-table-column label="操作" align="center">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="open_set(scope.row)">设置</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 弹窗 -->
        <el-dialog title="设置" :visible.sync="dialogFormVisible" size="tiny" :close-on-click-modal="false" v-loading="dalogi_loading">

            <el-form :model="dialogForm" :inline="true">
                <div>
                    <el-form-item label="用户ID" label-width="100px">
                        <el-input v-model="dialogForm.id" placeholder="用户ID" :disabled="true"></el-input>
                    </el-form-item>
                </div>

                <div>
                    <el-form-item label="用户昵称" label-width="100px">
                        <el-input v-model="dialogForm.nickname" placeholder="用户昵称" :disabled="true"></el-input>
                    </el-form-item>
                </div>

                <div>
                    <el-form-item label="用户积分" label-width="100px">
                        <el-input-number v-model="dialogForm.credits" :min="0" :max="9999999" placeholder="用户积分"></el-input-number>
                    </el-form-item>
                </div>

                <div>
                    <el-form-item label="登录状态" label-width="100px">
                        <el-select v-model="dialogForm.status" placeholder="状态" style="width:120px" clearable>
                            <el-option value="1" label="启用"></el-option>
                            <el-option value="2" label="禁用"></el-option>
                        </el-select>
                    </el-form-item>
                </div>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="send_set">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import { DatePicker, InputNumber } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-date-picker": DatePicker,
        "el-input-number": InputNumber,
    },
    data() {
        return {
            isSearch: false,
            dalogi_loading: false,
            dialogFormVisible: false,
            dialogForm: {
                id: 0,
                nickname: '',
                credits: 0,
                status: 0
            },
          
            formInline: {
                keyword: '',
                start_time: '',
                end_time: '',
                status: '',
                credits_sort: '',
                account_sort: ''
            },
            list: []
        }
    },
    methods: {
        //格式化日期范围
        fromDate(val) {
            if (val) {
                let _v = val.split(' - ');
                this.formInline.start_time = _v[0]
                this.formInline.end_time = _v[1]
            }
        },
        //打开设置
        open_set(data) {
            this.dialogForm = {
                id: data.id,
                nickname: data.nickname,
                credits: data.credits,
                status: data.status
            }
            this.dialogFormVisible = true;
        },
        //保存设置
        send_set() {
            let url = '/admin/User/handle', vm = this, data = this.dialogForm;
            vm.dalogi_loading = true;

            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$message.success(res.msg);
                    vm.dialogFormVisible = false;
                    if (vm.isSearch) {
                        vm.onSearch(vm.pages.current_page);
                    }else{
                        vm.get_list();
                    }
                } else {
                    vm.handleError(res)
                }
                vm.dalogi_loading = false;
            })

        },
        //清空
        onReset() {
            this.formInline = {
                keyword: '',
                start_time: '',
                end_time: '',
                status: '',
                credits_sort: '',
                account_sort: ''
            }
            this.value7 = '';
            this.get_list(1)
            this.isSearch = false;
        },
        //搜索
        onSearch(current_paged) {

            this.isSearch = true;
            current_paged = current_paged || 1;
            let searchData = this.formInline
            this.get_list(current_paged, searchData)
        },

        //取数据
        get_list(page, searchData) {
            page = page || 1;
            let url = '/admin/User/get_list?page=' + page,
                vm = this;

            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.list = res.data.list;
                    vm.pages = res.data.pages
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
        this.setBreadcrumb(['用户', '用户列表'])
        

    }

}
</script>
