<template>
    <div>

        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="提现申请" name="0"></el-tab-pane>
                <el-tab-pane label="已提现记录" name="2"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade" @keyup.enter="onSearch()">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户ID/手机号码">
                    <el-input v-model="formInline.keyword" placeholder="用户ID/手机号码" style="width:180px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="user_id" label="提现用户ID" width="120"></el-table-column>
            <el-table-column prop="nickname" label="提现人"></el-table-column>
            <el-table-column prop="phone_number" label="手机号码" width="120"></el-table-column>
            <el-table-column prop="account_balance" label="账户余额" width="80"></el-table-column>
            <el-table-column prop="amount" label="提现金额" width="80"></el-table-column>
            <el-table-column prop="add_time" label="提现申请时间" width="150"></el-table-column>
            <el-table-column prop="status" label="状态" width="80">
                <template scope="scope">
                    {{scope.row.status == 2?'已同意':scope.row.status == 3?'拒绝':'待处理'}}
                </template>
            </el-table-column>
            <el-table-column prop="handle_time" label="提现审核时间" width="150"></el-table-column>
            <el-table-column prop="admin_user_id" label="管理员ID" width="90"></el-table-column>
            <el-table-column prop="admin_user_name" label="管理员名称" width="120"></el-table-column>
            <el-table-column label="操作" width="120" align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="agree(scope.row)" v-if="scope.row.status == 1">同意</el-button>
                    <el-button type="text" size="small" @click="refuse(scope.row)" v-if="scope.row.status == 1">拒绝</el-button>
                </template>
            </el-table-column>
        </el-table>

        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            tabs: '',
            isSearch: false,
            dalogi_loading: false,
            formInline: {
                keyword: '',
            },
            list: []
        }
    },
    methods: {
        //选择标签页
        onSelectedTabs(tab) {
            let _name = tab.name;
            let _data = {
                status: _name
            }
            this.get_list(1, _data)

        },
        //同意
        agree(data){
            let msg = "1、点击确定后，系统将直接在该用户的账户余额中扣除该用户的提现金额。2、扣除提现金额后，您需要在线下转款给该用户";
            this.$confirm(msg, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.send_handle(data, 2);
            }).catch(() => {
                
            });
        },
        //拒绝
        refuse(data){
            this.$confirm('确定拒绝此提现申请?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.send_handle(data, 3);
            }).catch(() => {
                
            });
        },
        //保存数据
        send_handle(data, type) {
            let url = '/admin/Withdraw/handle', vm = this, save_data = {id:data.id, status:type};
            vm.dalogi_loading = true;

            this.apiPost(url, save_data).then(function(res) {
                if (res.code) {
                    vm.$message.success(res.msg);
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
            let url = '/admin/Withdraw/get_list?page=' + page,
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
        this.setBreadcrumb(['用户', '提现申请'])
        

    }

}
</script>
