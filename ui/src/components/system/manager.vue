<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="角色名称">
                    <el-input v-model="formInline.keyword" placeholder="角色名称" style="width:200px"></el-input>
                </el-form-item>

              

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
            <el-button type="warning" class="goods_add_btn" @click="open(false)">添加管理员</el-button>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading" :row-class-name="tableRowClassName">
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="nickname" label="管理员姓名" width="150"></el-table-column>
            <el-table-column prop="user_name" label="后台登录账号" width="150"></el-table-column>
            <el-table-column prop="role_name" label="所属角色" width="180"></el-table-column>
            <el-table-column prop="login_time" label="最近登录时间" width="180"></el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="180"></el-table-column>
            <el-table-column prop="status" label="状态" width="150">
                <template scope="scope">
                    <el-radio-group v-model="scope.row.status" size="small" @change="changeStatus(scope.row)">
                        <el-radio-button :label="1">启用</el-radio-button>
                        <el-radio-button :label="2">禁用</el-radio-button>
                    </el-radio-group>
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button type="text" size="small" @click="viewCtrl(scope.row.role_id)">查看权限</el-button>
                    <el-button type="text" size="small" @click="open(true,scope.row)">修改</el-button>
                    <el-button type="text" size="small" @click="onRemove(scope.row)">删除</el-button>
                </template>
            </el-table-column>

        </el-table>
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 添加管理员 -->
        <el-dialog title="添加管理员" :visible.sync="dialogFormVisible" :open="open">
            <el-form label-width="100px">
                <el-form-item label="管理员姓名">
                    <el-input v-model="dialog.nickname" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="后台登录账号">
                    <el-input v-model="dialog.user_name" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="密码">
                    <el-input v-model="dialog.password" placeholder="不输入则不修改" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" v-if="dialog.password">
                    <el-input v-model="dialog.repassword" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="所属角色">
                    <el-select v-model="dialog.role_id" placeholder="请选择">
                        <el-option v-for="item in role_list" :key="item.id" :label="item.role_name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-radio-group v-model="dialog.status">
                        <el-radio :label="1">启用</el-radio>
                        <el-radio :label="2">禁用</el-radio>
                    </el-radio-group>

                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="save_menu">确 定</el-button>
            </div>
        </el-dialog>

        <!-- 查看权限 -->
        <el-dialog title="查看权限" :visible.sync="dialogFormVisible2">
            <el-tree ref="tree" :data="menu_list" show-checkbox node-key="id">
            </el-tree>
            <div slot="footer" class="dialog-footer">

                <el-button @click="dialogFormVisible2 = false">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import { Tree } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-tree": Tree,

    },
    data() {
        return {
            isSearch: false,
            formInline: {
                keyword: ''
            },
            menu_list: [],

            dialogFormVisible: false,
            dialogFormVisible2: false,
            dialogLoading: false,
            role_list: [],
            dialog: {
                nickname: '',
                user_name: '',
                repassword: '',
                password: '',
                role_id: '',
                status: ''
            },
            list: []
        }
    },
    methods: {
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
            let url = '/admin/Manager/get_list?page=' + page,
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
        onRemove(item) {
            let vm = this;
            this.$confirm('你确定要删除该用户, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                vm.removeData(item)

            }).catch(() => {
            });

        },
        //删除
        removeData(item) {

            let url = '/admin/manager/del_user?user_id=' + item.id,
                vm = this;
            vm.loading = true;
            this.apiGet(url).then(function(res) {
                if (res.code) {

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
        },
        open(isEdit, item) {
            this.dialogFormVisible = true
            let vm = this;

            setTimeout(function() {
                if (isEdit) {
                    vm.dialog.nickname = item.nickname
                    vm.dialog.user_name = item.user_name
                    vm.dialog.role_id = item.role_id
                    vm.dialog.user_id = item.id
                    vm.dialog.status = item.status
                    vm.dialog.password = ''
                    vm.dialog.repassword = ''


                } else {
                    vm.dialog.nickname = '';
                    vm.dialog.user_name = '';
                    vm.dialog.status = 1

                }

            }, 100)


        },
        changeStatus(item) {


            let vm = this, url = '/admin/manager/modify_user', data = {
                user_id: item.id,
                status: item.status
            };
            this.apiPost(url, data).then((res) => {
                vm.dialogLoading = false
                if (res.code) {
                    vm.$message.success(res.msg);
                    vm.dialogFormVisible = false;

                } else {
                    vm.handleError(res)
                }
            })
        },
        get_role_list() {
            let vm = this, url = '/admin/manager/add_user';
            this.apiGet(url).then((res) => {
                if (res.code) {
                    vm.role_list = res.data.role;
                } else {
                    vm.handleError(res)
                }
            })
        },
        save_menu() {
            let vm = this, url = '/admin/manager/save_user';
            this.apiPost(url, vm.dialog).then((res) => {
                vm.dialogLoading = false
                if (res.code) {
                    vm.$message.success(res.msg);
                    vm.dialogFormVisible = false;
                    vm.get_list();
                } else {
                    vm.handleError(res)
                }
            })
        },
        viewCtrl(role_id) {
            this.dialogFormVisible2 = true;
            let vm = this, url = '/admin/manager/view_ctrl?role_id=' + role_id;
            this.apiGet(url).then((res) => {
                if (res.code) {
                    vm.menu_list = res.data.menu;
                    let _role_ids = res.data.auth
                    vm.$refs.tree.setCheckedKeys(_role_ids);
                } else {
                    vm.handleError(res)
                }
            })
        }

    },
    //组件初始化
    created() {
        this.get_list();
        this.get_role_list();
        this.setBreadcrumb(['系统', '管理员'])
        
    }

}
</script>
