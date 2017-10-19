<template>
    <div>
        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="商品评论" name="0"></el-tab-pane>
                <el-tab-pane label="已删除的评论" name="1"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label-width="1">
                    <el-input v-model="formInline.keyword" placeholder="订单编号/用户手机" style="width:200px"></el-input>
                </el-form-item>

                <el-form-item label="申请时间">
                    <el-date-picker v-model="value7" type="daterange" align="right" placeholder="选择日期范围" @change="fromDate" :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
        </div>

        <!-- 表格 -->
        <el-table :data="list" border style="width: 100%" v-loading="loading">
            <el-table-column prop="good_link" label="商品页面链接" width="200">
                <template scope="scope">
           
                       
                    <el-popover   placement="right" trigger="hover">
                         <div>
                             <vue-qr :text="or(scope.row.good_id)" height="200" width="200"></vue-qr>
                             <div style="text-align:center;color:#666">用微信扫一扫</div>
                         </div>
                         <el-button type="text"  slot="reference" >/mini/#/detail/id/{{scope.row.good_id}}</el-button>
                    </el-popover>
                    
                </template>
            </el-table-column>
            <el-table-column prop="user_id" label="用户ID" width="120"></el-table-column>
            <el-table-column prop="add_time" label="评论时间" width="180"></el-table-column>
            <el-table-column prop="content" label="评论内容" width="300"></el-table-column>
            <el-table-column prop="nickname" label="处理员" width="150"></el-table-column>
            <el-table-column prop="delete_time" label="处理时间"></el-table-column>
            <el-table-column label="操作" width="100">
                <template scope="scope">
                    <el-button type="text" v-if="scope.row.delete_time == null" size="small" @click="onDel(scope.row.id)">删除评论</el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

    </div>
</template>

<script>
import VueQr from 'vue-qr'
import http from '@/assets/js/http'
import { DatePicker,Popover } from 'element-ui'
export default {
    mixins: [http],
    components: {
        VueQr,
        'el-popover':Popover,
        "el-date-picker": DatePicker,
    },

    data() {
        return {
            tabs: '',
            isSearch: false,
            formInline: {
                keyword: '',
                start_time: '',
                end_time: '',
                delete:''

            },
            list: []
        }
    },

    methods: {
        or(id){
            let a = window.location.origin;
             return   a +'/mini/#/detail/id/'+id
        },
        //格式化日期范围
        fromDate(val) {
            if (val) {
                let _v = val.split(' - ');
                this.formInline.start_time = _v[0]
                this.formInline.end_time = _v[1]
            }
        },

        fromDate3(val) {
            this.dialogForm.handle_time = val
        },
        //选择标签页
        onSelectedTabs(tab) {
            let _name = tab.name == 0 ? "" : tab.name;
          
            let _data = {
                delete: _name
            }
            this.get_list(1, _data)

        },


        //清空搜索
        onReset() {
            this.formInline = {
                keyword: '',
                status: '',
                start_time: '',
                end_time: ''
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
            let url = '/admin/Goodcomment/get_list?page=' + page,
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
        onDel(id) {
            this.$confirm('是否删除该评论?', '提示', {
                confirmButtonText: '确定删除',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.onHandle(id)
            }).catch(() => {
            });
        },

        //删除评论
        onHandle(id) {
            let url = '/admin/Goodcomment/del?id='+id,
                vm = this;
            vm.loading = true;
            this.apiGet(url, ).then(function(res) {
                if (res.code) {
                    vm.dialogFormVisible = false;

                    vm.$message({
                        type: 'success',
                        message: res.msg
                    });
                    vm.get_list();
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
        this.setBreadcrumb(['订单', '商品评论'])
        
    }

}
</script>
