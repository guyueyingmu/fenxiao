<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="分销商用户ID">
                    <el-input v-model="formInline.keyword" placeholder="分销商用户ID" style="width:170px"></el-input>
                </el-form-item>
                <el-form-item label="手机号码">
                    <el-input v-model="formInline.user_phone" placeholder="手机号码" style="width:170px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
         

        </div>

        <el-table :data="list"  border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="用户ID"></el-table-column>
            <el-table-column prop="nickname" label="用户昵称"></el-table-column>
            <el-table-column prop="phone_number" label="手机号码"></el-table-column>
            <el-table-column prop="distributor_time" label="添加时间" width="180"></el-table-column>
            <el-table-column prop="account_balance" label="账户余额"></el-table-column>
            <el-table-column prop="earn_total" label="分成总额"></el-table-column>
            <el-table-column prop="p_user" label="上级分销商"></el-table-column>
            <el-table-column prop="c_total" label="一级会员总数"></el-table-column>
            <el-table-column prop="c_total2" label="二级会员总数"></el-table-column>
            <el-table-column prop="sepcial_dis" label="特殊分销商">
                <template slot-scope="scope">
                    <el-radio-group v-model="scope.row.sepcial_dis" size="small" @change="changeStatus(scope.row)">
                        <el-radio-button :label="'1'">是</el-radio-button>
                        <el-radio-button :label="'2'">否</el-radio-button>
                    </el-radio-group>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="200" align="center">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="open_win(scope.row)">分销二维码</el-button>
                    <el-button type="text" size="small" @click="goto('/distributor/user_id/'+scope.row.id+'/level/1')">我的会员</el-button>
                </template>
            </el-table-column>

        </el-table>
        <div class="pagination">

            <el-pagination v-if="parseInt(pages.total_page,10) > 1"  @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 弹窗 -->
        <el-dialog title="分销二维码" :visible.sync="dialogFormVisible" :close-on-click-modal="false" v-loading="dalogi_loading">
            
            <div class="dialog_main" style="text-align:center;">
                <div>把分销二维码分享给潜在客户，客户扫描后即成为该代理商的会员</div>
                <div style="padding:10px 0;"><img style="width:200px;" :src="show_qrcode" /></div>
                <div style="font-size:16px;">{{show_user_name}}的推广二维码</div>
                <div style="color:#8492A6;text-align:left; padding-top:15px;">备注：可以在微信公众号中查看自己的分销二维码</div>
            </div>

            <div slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
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
            isSearch: false,
            dialogFormVisible: false,
            dalogi_loading: false,
            show_qrcode: '',
            show_user_name: '',
            formInline: {
                keyword: '',
                user_phone: '',
            },
            list: []
        }
    },
    methods: {
        changeStatus(item) {
            let vm = this, url = '/admin/Userdis/special_dis', data = {
                id: item.id,
                status: item.sepcial_dis
            };
            this.apiPost(url, data).then((res) => {
                vm.dialogLoading = false
                if (res.code) {
                    vm.$message.success(res.msg);

                } else {
                    vm.handleError(res)
                }
            })
        },
        open_win(data){
            this.show_qrcode = data.dis_qrcode;
            this.show_user_name = data.nickname;
            this.dialogFormVisible = true;
        },
        //清空
        onReset() {
            this.formInline = {
                keyword: '',
                user_phone: '',
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
            let url = '/admin/Userdis/get_list?page=' + page,
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
        this.setBreadcrumb(['分销', '分销商列表'])
        
    }

}
</script>
