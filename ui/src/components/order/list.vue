<template>
    <div>
        <div class="tabs_p">
            <el-tabs v-model="tabs" type="card" @tab-click="onSelectedTabs">
                <el-tab-pane label="全部订单" name="0"></el-tab-pane>
                <el-tab-pane label="待处理订单" name="1"></el-tab-pane>
                <el-tab-pane label="已服务订单" name="2"></el-tab-pane>
                <el-tab-pane label="已取消订单" name="4"></el-tab-pane>
                <el-tab-pane label="已完成订单" name="5"></el-tab-pane>
            </el-tabs>
        </div>

        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label-width="1">
                    <el-input v-model="formInline.keyword" placeholder="订单编号/用户手机" style="width:200px"></el-input>
                </el-form-item>
                <el-form-item label="订单状态">
                    <el-select v-model="formInline.order_status" placeholder="订单状态" style="width:120px" clearable>

                        <el-option :value="1" label="待处理"></el-option>
                        <el-option :value="2" label="已服务"></el-option>
                        <el-option :value="3" label="已发货"></el-option>
                        <el-option :value="4" label="已取消"></el-option>
                        <el-option :value="5" label="已完成"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="支付状态">
                    <el-select v-model="formInline.pay_status" placeholder="支付状态" style="width:120px" clearable>
                        <el-option :value="1" label="未支付"></el-option>
                        <el-option :value="2" label="已支付"></el-option>
                        <el-option :value="3" label="已退费"></el-option>
                        <el-option :value="4" label="支付失败"></el-option>
                        <el-option :value="5" label="退费失败"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="下单日期">
                    <el-date-picker v-model="value7" type="daterange" align="right" placeholder="选择日期范围" @change="fromDate" :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
            <el-button type="warning" class="goods_add_btn" @click="goto('/goods_add')">添加商品</el-button>

        </div>

        <div class="ui-table-wrapper" style="width: 100%" v-loading.body="loading">
            <template v-if="list && list.length">

                <template v-for="(item,idx) in list">
                    <div class="table_list" :key="item.id">
                        <table :class="{'bg':item.order_status == 1}">
                            <colgroup>
                                <col width="100">
                                <col width="100">
                                <col width="120">
                                <col width="150">
                                <col width="120">
                                <col width="100">
                                <col width="100">
                                <col width="100">
                                <col>
                                <col width="120">
                                <col width="120">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>订单编号</th>
                                    <th class="center">下单用户ID</th>
                                    <th>用户手机</th>
                                    <th>下单时间</th>
                                    <th>订单总额</th>
                                    <th>订单状态</th>
                                    <th>支付状态</th>
                                    <th>订单来源</th>
                                    <th>支付方式</th>
                                    <th>完成时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{item.order_number}}</td>
                                    <td class="center">{{item.user_id}}</td>
                                    <td>{{item.phone_number}}</td>
                                    <td>{{item.add_time}}</td>
                                    <td>{{item.total_amount}}</td>
                                    <td>
                                        <span :class="{'red': item.order_status == 1}">{{item.order_status_txt }}</span>
                                    </td>
                                    <td>{{item.pay_status_txt }}</td>
                                    <td>{{item.order_from_txt}}</td>
                                    <td>{{item.pay_method_txt }}</td>
                                    <td>{{item.finish_time}}</td>
                                    <td class="center tool_no_border">
                                        <el-button type="text" size="small">详情</el-button>
                                        <el-button type="text" size="small">取消订单</el-button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="nopadding">
                                        <table>
                                            <colgroup>
                                                <col width="95">
                                                <col width="98">
                                                <col>
                                                <col>
                                                <col width="180">
                                                <col width="100">
                                                <col width="200">
                                            </colgroup>
                                            <thead>
                                                <tr class="bg">
                                                    <th>商品编号</th>
                                                    <th class="center">商品图标</th>
                                                    <th class="center">商品名称</th>
                                                    <th class="center">商品分类</th>
                                                    <th class="center">购买单价</th>
                                                    <th class="center">购买数量</th>
                                                    <th class="center" style="border-bottom-width: 1px;">操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(goods,goods_idx) in item.orders_goods" :key="goods.id">
                                                    <td class="center">{{goods.good_id}}</td>
                                                    <td class="center"><img :src="goods.good_img" width="40" height="40"></td>
                                                    <td class="center">{{goods.good_name}}</td>
                                                    <td class="center">{{goods.cat_name}}</td>
                                                    <td class="center">{{goods.price}}</td>
                                                    <td class="center">{{goods.buy_num}}</td>

                                                    <td class="center tool_no_border" v-if="goods_idx == 0" :rowspan="item.orders_goods.length">
                                                        <el-button size="small" @click="killOrder">立即处理</el-button>

                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </template>
            <div class="table_nodata" v-else>
                没有数据
            </div>

        </div>

        <!-- 分页 -->
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 弹窗 -->
        <el-dialog title="实物类商品--发货"  :visible.sync="dialogFormVisible">
            <el-form :model="dialogForm" :inline="true">
                <el-form-item label="活动名称">
                    <el-input v-model="dialogForm.name" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="活动区域" >
                    <el-select v-model="dialogForm.region" placeholder="请选择活动区域">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
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
            dialogForm:{
                name:'',
                region:''

            },
            dialogFormVisible:false,
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
            tabs: '',
            value7: '',
            isSearch: false,
            formInline: {
                order_status: '',
                keyword: '',
                pay_status: '',
                start_time: '',
                end_time: ''

            },
            list: []
        }
    },
    methods: {
        //处理订单
        killOrder(){
            this.dialogFormVisible = true;
        },
        fromDate(val) {
            if (val) {
                let _v = val.split(' - ');
                this.formInline.start_time = _v[0]
                this.formInline.end_time = _v[1]
            }

        },
        onSelectedTabs(tab) {
            let _name = tab.name;
            let _data = {
                order_status: _name
            }
            this.get_list(1, _data)

        },
        //设置下架状态样式
        tableRowClassName(row, index) {
            if (row.status == 2) {
                return 'status_off'
            } else {
                return ''
            }

        },
        //表格设置分类名
        getType(good_type_id) {
            let id = parseInt(good_type_id, 10)
            return this.$store.getters.GOODTYPE[id - 1].label;
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
                status: ''
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
            let url = '/admin/order/get_list?page=' + page,
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

        //删除确认
        onRemove(index) {
            let vm = this;
            this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                vm.removeData(index)

            }).catch(() => {
            });

        },
        //删除
        removeData(index) {
            let _data = this.list[index]
            let url = '/admin/order/del/good_id/' + _data.id,
                vm = this;
            vm.loading = true;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.list.splice(index, 1)
                    vm.$message({
                        type: 'success',
                        message: res.msg
                    });
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
        this.setBreadcrumb(['订单', '订单列表'])
        this.setMenu('1-0');
    }

}
</script>
