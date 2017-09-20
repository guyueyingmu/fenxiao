<template>
    <div>
        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="分成日志" name="0"></el-tab-pane>
                <el-tab-pane label="已获佣" name="2"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="获佣分销商用户ID">
                    <el-input v-model="formInline.keyword" placeholder="获佣分销商用户ID" style="width:140px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
         

        </div>

        <el-table :data="list"  border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="order_number" label="订单编号"></el-table-column>
            <el-table-column prop="order_user_id" label="下单用户ID" width="110"></el-table-column>
            <el-table-column prop="good_id" label="商品ID"></el-table-column>
            <el-table-column prop="earn_amount" label="获佣金额">            
                <template scope="scope">
                    {{scope.row.status === 2?scope.row.earn_amount_input:scope.row.earn_amount}}
                </template>
            </el-table-column>
            <el-table-column prop="earn_user_id" label="获佣分销商用户ID" width="150"></el-table-column>
            <el-table-column prop="level" label="获佣级别">                
                <template scope="scope">
                    {{scope.row.level === 1?'一级获佣':'二级获佣'}}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="获佣状态">                
                <template scope="scope">
                    {{scope.row.status === 1?'等待获佣':'已获佣'}}
                </template>
            </el-table-column>
            <el-table-column prop="earn_time" label="获佣时间" width="170"></el-table-column>
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
            tabs: '',
            isSearch: false,
            dialogFormVisible: false,
            dalogi_loading: false,
            formInline: {
                keyword: '',

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
            let url = '/admin/Orderdislog/handle', vm = this, data = this.dialogForm;
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
            this.get_list(1, _data)

        },
   
        //清空
        onReset() {
            this.formInline = {
                keyword: '',
            }
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
            let url = '/admin/Orderdislog/get_list?page=' + page,
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
        this.setBreadcrumb(['分销', '分成日志'])
        
    }

}
</script>
