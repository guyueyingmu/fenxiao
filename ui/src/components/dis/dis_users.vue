<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户ID/手机号码">
                    <el-input v-model="formInline.keyword" placeholder="用户ID/手机号码" style="width:120px"></el-input>
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
            <el-table-column prop="dis_scan_time" label="分销扫码时间"></el-table-column>
            <el-table-column prop="pid" label="所属分销商用户ID"></el-table-column>
            <el-table-column prop="distribution_level" label="分销等级">
                <template scope="scope">
                    {{scope.row.distribution_level === 1?'会员':'分销商'}}
                </template>
            </el-table-column>
            <el-table-column prop="distributor_time" label="设为分销商时间"></el-table-column>
            <el-table-column label="操作" width="100" align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="open_set(scope.row)" v-if="scope.row.distribution_level == 1">设为分销商</el-button>
                    <el-button type="text" size="small" @click="open_set(scope.row)" v-if="scope.row.distribution_level == 2">退出分销商</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination">

            <el-pagination v-if="parseInt(pages.total_page,10) > 1"  @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
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
            isSearch: false,
            formInline: {
                good_type: '',
                keyword: '',
                cat_id: '',

            },
            list: []
        }
    },
    methods: {
        open_set(){

        },

        //currentPage 改变时会触发
        handleCurrentChange(current_paged) {
        
            if (this.isSearch) {
                this.onSearch(current_paged)
            } else {
                this.get_list(current_paged)
            }
        },
        //清空
        onReset() {
            this.formInline = {
                goods_type: '',
                keyword: '',
                cat_id: '',
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
            let url = '/admin/Userdismember/get_list?page=' + page,
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
        }

    },
    //组件初始化
    created() {
        this.get_list();
        this.setBreadcrumb(['分销', '分销会员列表'])
        this.setMenu('3-1');
    }

}
</script>
