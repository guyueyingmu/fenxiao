<template>
    <div>
        <div class="page_heade" @keyup.enter="onSearch()">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户ID">
                    <el-input v-model="formInline.keyword" placeholder="用户ID" style="width:180px"></el-input>
                </el-form-item>

                <el-form-item label="获得时间">
                    <el-date-picker v-model="value7" type="daterange" align="right" placeholder="选择日期范围" @change="fromDate" :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="user_id" label="用户ID"></el-table-column>
            <el-table-column prop="nickname" label="用户昵称"></el-table-column>
            <el-table-column prop="credits_in" label="收入积分"></el-table-column>
            <el-table-column prop="credits_out" label="支出积分"></el-table-column>
            <el-table-column prop="credits_from_txt" label="渠道"></el-table-column>
            <el-table-column prop="track_id" label="track"></el-table-column>
            <el-table-column prop="add_time" label="获得时间"></el-table-column>
        </el-table>

        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import { DatePicker } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-date-picker": DatePicker,
    },
    data() {
        return {
            isSearch: false,
            dalogi_loading: false,
            formInline: {
                keyword: '',
                start_time:'',
                end_time:''
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
        //清空
        onReset() {
            this.formInline = {
                keyword: '',
                start_time:'',
                end_time:''
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
            let url = '/admin/Credits/get_list?page=' + page,
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
        this.setBreadcrumb(['用户', '积分记录'])
        

    }

}
</script>
