<template>
    <div>
        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="换货申请" name="1"></el-tab-pane>
                <el-tab-pane label="已换货" name="2"></el-tab-pane>
                <el-tab-pane label="已拒绝" name="3"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label-width="1">
                    <el-input v-model="formInline.keyword" placeholder="订单编号" style="width:200px"></el-input>
                </el-form-item>
                <el-form-item label-width="1">
                    <el-input v-model="formInline.user_phone" placeholder="用户手机" style="width:200px"></el-input>
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
            <el-table-column prop="order_number" label="订单编号" width="120"></el-table-column>
            <el-table-column prop="order_status_txt" label="订单状态" width="120"></el-table-column>
            <el-table-column prop="pay_status_txt" label="支付状态" width="120"></el-table-column>
            <el-table-column prop="user_id" label="下单用户ID" width="120"></el-table-column>
            <el-table-column prop="phone_number" label="用户手机"></el-table-column>
            <el-table-column prop="add_time" label="申请时间"> </el-table-column>
            <el-table-column prop="handle_user" label="处理员" width="100"> </el-table-column>
            <el-table-column prop="status" label="处理状态" width="100">
                <template scope="scope">
                    <span v-if="scope.row.status == 1">未处理</span>
                    <span v-else-if="scope.row.status == 2">同意</span>
                    <span v-else>拒绝</span>
                </template>
            </el-table-column>
            <el-table-column prop="handle_time" label="处理时间"></el-table-column>
            <el-table-column prop="handle_note" label="处理备注"></el-table-column>
            <el-table-column label="操作" width="100">
                <template scope="scope">
                    <el-button type="text" v-if="scope.row.status == 1 " size="small" @click="onExchange(scope.row,scope.row.$index)">立即处理</el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- 分页 -->
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 弹窗 -->
        <el-dialog title="换货" :visible.sync="dialogFormVisible" :close-on-click-modal="false" v-loading="dalogi_loading" size="small">

            <!-- 要服务 2 5 -->
            <el-form :model="dialogForm" :inline="true">
                <el-form-item label="处理员" label-width="100px">
                    <el-input v-model="dialogForm.handle_user" auto-complete="off" placeholder="发货员"></el-input>
                </el-form-item>

                <el-form-item label="处理时间" label-width="100px">
                    <el-date-picker v-model="dialogForm.handle_time" type="datetime" @change="fromDate3" :editable="false" placeholder="选择日期"></el-date-picker>
                </el-form-item>
                <el-form-item label="处理状态" label-width="100px">
                    <el-select v-model="dialogForm.status" placeholder="处理状态" style="width:193px">
                        <el-option label="未处理" :value="1"></el-option>
                        <el-option label="同意" :value="2"></el-option>
                        <el-option label="拒绝" :value="3"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="备注" label-width="100px">
                    <el-input v-model="dialogForm.handle_note" auto-complete="off" placeholder="备注">
                    </el-input>
                </el-form-item>
            </el-form>
            <div class="dialog_main">
                <h4 class="dialog_order_title">订单信息</h4>
                <div class="dialog_order_info">
                    <el-row>
                        <el-col :span="12">
                            订单编号：
                            <span>{{dialog_temp.order_number }}</span> <br> 下单时间：
                            <span>{{ dialog_temp.order_add_time }}</span>
                        </el-col>
                        <el-col :span="12">
                            下单用户：
                            <span>{{dialog_temp.nickname }}</span> <br> 用户手机：
                            <span>{{ dialog_temp.phone_number }}</span>
                        </el-col>
                    </el-row>
                </div>
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="dialog_ok">确 定</el-button>
            </div>
        </el-dialog>

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
            dalogi_loading: false,
            dialog_temp: {},
            dialogFormVisible: false,
            dialogForm: {},
            tabs: '',
            value8: '',
            isSearch: false,
            formInline: {
                keyword: '',
                user_phone: '',
                start_time: '',
                end_time: ''

            },
            list: []
        }
    },
    methods: {
        //换货弹窗
        onExchange(item, idx) {
            this.dialogForm = {
                handle_user: '',
                handle_time: '',
                handle_note: '',
                status: '',
                id: ''
            }
            this.dialogFormVisible = true;
            this.dialog_temp = item;
            this.dialogForm.id = item.id;


        },
        //确定弹窗
        dialog_ok() {
            let _data = this.dialogForm;
            this.onHandle(_data)
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
                status: _name
            }
            this.get_list(1, _data)

        },

    
        //清空搜索
        onReset() {
            this.formInline = {
                keyword: '',
                user_phone: '',
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
            let url = '/admin/exchange/get_list?page=' + page,
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

        
        onHandle(data, cb) {

            let url = '/admin/exchange/handle',
                vm = this;
            vm.loading = true;
            this.apiPost(url, data).then(function(res) {
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
        this.setBreadcrumb(['订单', '换货申请'])
        
    }

}
</script>
