<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="操作人">
                    <el-select v-model="formInline.keyword" placeholder="操作人" style="width:100px" clearable>
                        <el-option :value="item.id" :label="item.nickname" v-for="item in admin_users" :key="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="操作时间">
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
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="admin_user_name" label="操作人" width="150"></el-table-column>
            <el-table-column prop="log_time" label="操作时间" width="170"></el-table-column>
            <el-table-column prop="menu_name" label="操作菜单名称" width="150"></el-table-column>
            <el-table-column prop="content" label="操作内容">
                <template scope="scope">
                    <div>{{scope.row.content2.title}}</div>
                    <div>{{scope.row.content2.data}}</div>


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
import { DatePicker } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-date-picker": DatePicker,
    },
    data() {
        return {
            isSearch: false,
            formInline: {
                keyword: '',
                starttime: '',
                endtime: '',

            },
            list: [],
            pickerOptions: {
                shortcuts: [
                    {
                        text: '今天',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime());
                            picker.$emit('pick', [start, end]);
                        }
                    },
                    {
                        text: '最近三天',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 3);
                            picker.$emit('pick', [start, end]);
                        }
                    },
                    {
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
            },
            value7: '',
            admin_users:[]
        }
    },
    methods: {
        //格式化日期范围
        fromDate(val) {
            if (val) {
                let _v = val.split(' - ');
                this.formInline.starttime = _v[0]
                this.formInline.endtime = _v[1]
            }
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
                keyword: '',
                starttime: '',
                endtime: '',
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
            let url = '/admin/Logrecord/get_list?page=' + page,
                vm = this;
            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    for(let i=0;i<res.data.list.length;i++){
                        res.data.list[i].content2 = JSON.parse(res.data.list[i].content)
                    }

                    vm.list = res.data.list;
                    vm.admin_users = res.data.admin_user;
                
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
        this.setBreadcrumb(['系统', '日志'])
        this.setMenu('4-2');
    }

}
</script>
