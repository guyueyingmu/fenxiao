<template>
    <div>
        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="分销总额奖" name="1"></el-tab-pane>
                <el-tab-pane label="已获佣" name="2"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="分销商用户ID">
                    <el-input v-model="formInline.keyword" placeholder="分销商用户ID" style="width:140px"></el-input>
                </el-form-item>
                <el-form-item label="分销商手机">
                    <el-input v-model="formInline.user_phone" placeholder="分销商手机" style="width:140px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
         

        </div>

        <el-table :data="list"  border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="earn_user_id" label="用户ID"></el-table-column>
            <el-table-column prop="nickname" label="用户呢称"></el-table-column>
            <el-table-column prop="phone_number" label="手机号码" width="110"></el-table-column>
            <el-table-column prop="earn_time" label="团队奖获得日期"></el-table-column>
            <el-table-column prop="earn_amount" label="获得金额">            
                <template scope="scope">
                    {{scope.row.status == 2?scope.row.earn_amount_input:scope.row.earn_amount}}
                </template>
            </el-table-column>
            <el-table-column prop="member_num" label="发展会员数" width="150"></el-table-column>
            <el-table-column prop="level" label="获奖级别">                
                <template scope="scope">
                    {{scope.row.level == 1?'一级获佣':'二级获佣'}}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="获佣状态">                
                <template scope="scope">
                    {{scope.row.status == 1?'等待获佣':'已获佣'}}
                </template>
            </el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="170"></el-table-column>
            <el-table-column prop="admin_user_id" label="管理员ID"></el-table-column>
            <el-table-column prop="admin_user_name" align="center" label="管理员名称"></el-table-column>
            <el-table-column label="操作" align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="open_win(scope.row)" v-if="scope.row.status == 1">确定获佣</el-button>
                </template>
            </el-table-column>

        </el-table>
        <div class="pagination">

            <el-pagination v-if="parseInt(pages.total_page,10) > 1"  @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <div style="color:#8492A6">
            备注： 每个月由系统统计当月分销商是否完成团队奖指标（分销设置中设置），完成则计算奖励。
        </div>

        <!-- 弹窗 -->
        <el-dialog title="提示" :visible.sync="dialogFormVisible" :close-on-click-modal="false" v-loading="dalogi_loading">
            
            <el-form :model="dialogForm" :inline="true">
                <el-form-item label="获佣金额" label-width="100px">
                    <el-input v-model="dialogForm.earn_amount" auto-complete="off" placeholder="获佣金额"></el-input>
                </el-form-item>
            </el-form>

            <div style="color:#8492A6; padding-top:15px;">确定获佣后，则添加金额进该获佣分销商的获佣总额和账户余额！</div>

            <div slot="footer" class="dialog-footer">
                <el-button type="primary" @click="post_handle()">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            tabs: '1',
            isSearch: false,
            dialogFormVisible: false,
            dalogi_loading: false,
            formInline: {
                keyword: '',
                user_phone: '',
                status: 1,
            },
            list: [],
            dialogForm: {
                id: '',
                earn_amount: '',
            },
        }
    },
    methods: {
        open_win(data){
            this.dialogForm.id = data.id;
            this.dialogForm.earn_amount = data.earn_amount;
            this.dialogFormVisible = true;
        },
        //保存数据
        post_handle() {
            let url = '/admin/Teamprize/handle', vm = this, data = this.dialogForm;
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

        //选择标签页
        onSelectedTabs(tab) {
            let _name = tab.name;
            let _data = {
                status: _name
            }
            this.formInline.status = _name;
            this.get_list(1, _data)

        },
   
        //清空
        onReset() {
            this.formInline.keyword = '';
            this.formInline.user_phone = '';             
            this.get_list(1, this.formInline)
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
            let url = '/admin/Teamprize/get_list?page=' + page,
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
        this.get_list(1, {status: 1});
        this.setBreadcrumb(['分销', '分销团队奖'])
        
    }

}
</script>
