<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户ID/手机号码">
                    <el-input v-model="formInline.keyword" placeholder="用户ID/手机号码" style="width:140px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="用户ID"></el-table-column>
            <el-table-column prop="nickname" label="用户昵称"></el-table-column>
            <el-table-column prop="phone_number" label="手机号码"></el-table-column>
            <el-table-column prop="dis_scan_time" label="分销扫码时间" width="180"></el-table-column>
            <el-table-column prop="pid" label="所属分销商用户ID"></el-table-column>
            <el-table-column prop="distribution_level" label="分销等级">
                <template slot-scope="scope">
                    {{scope.row.distribution_level == 1?'会员':scope.row.distribution_level == 2?'分销商':''}}
                </template>
            </el-table-column>
            <el-table-column prop="distributor_time" label="设为分销商时间"></el-table-column>
            <el-table-column label="操作" width="200" align="center">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="goto('/distributor/user_id/'+scope.row.id+'/level/2')" v-if="level == 1 && scope.row.distribution_level == 2">查看二级会员</el-button>
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
            user_id: 0,
            level: '',
            isSearch: false,
            formInline: {
                keyword: '',
            },
            list: []
        }
    },
    methods: {


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
            let url = '/admin/Userdis/get_member_list?id=' + this.user_id + '&type=' + this.level + '&page=' + page,
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
        initData() {
            this.user_id = this.$route.params.user_id;
            this.level = this.$route.params.level;
            let bread = '我的一级会员';
            if (this.level == 2) {
                bread = '我的二级会员';
            }
            this.get_list();
            this.setBreadcrumb(['分销', '分销商列表', bread])
            
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
