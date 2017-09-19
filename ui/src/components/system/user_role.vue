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

            <el-button type="warning" class="goods_add_btn" @click="open(false)">添加角色</el-button>
        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="role_name" label="角色名称" width="150"></el-table-column>
            <el-table-column prop="menu_auth_name" label="菜单权限">
                  <template scope="scope">
                      <div style="padding:10px 0">
                            <el-tag v-for="item in scope.row.menu_auth_name" type="primary" class="mytag" :key="item">{{item}}</el-tag>
                      </div>
                </template>
            </el-table-column>
            <el-table-column prop="" label="操作" width="250">
                <template scope="scope">
                    <el-button type="text" @click="open(true,scope.row)">修改</el-button>
                    <el-button type="text" @click="onRemove(scope.row)">删除</el-button>
                </template>
            </el-table-column>

        </el-table>
        <div class="pagination">

            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 添加角色 -->
        <el-dialog title="添加角色" :visible.sync="dialogFormVisible" :open="open">
            <el-form label-width="100px">
                <el-form-item label="角色名称">
                    <el-input v-model="dialog.role_name" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="活动区域" v-loading="dialogLoading">
                    <el-tree ref="tree" :data="menu_list" show-checkbox node-key="id">
                    </el-tree>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="save_menu">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import { Tree,Tag } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-tree": Tree,
        'el-tag':Tag
    },
    data() {
        return {
            isSearch: false,
            dialogFormVisible: false,
            dialogLoading: false,
            menu_list: [],
            dialog: {
                role_name: '',
                menu_auth: ''
            },
            formInline: {
                keyword: '',
            },
            list: []
        }
    },
    methods: {

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
            let url = '/admin/role/get_list?page=' + page,
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
            this.$confirm('该角色已被使用，如需删除，请把使用该角色的管理员调整为其他角色！', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                alert()
                vm.removeData(item)

            }).catch(() => {
            });
        },
        //删除
        removeData(item) {
          
            let url = '/admin/role/del_role/role_id/' + item.id,
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
                    vm.dialog.role_name = item.role_name
                    vm.dialog.role_id = item.id
                    let _menu_list = item.menu_auth.split(',')
                    vm.$refs.tree.setCheckedKeys(_menu_list);
                } else {
                        vm.dialog.role_id = '';
                    vm.dialog.role_name = '';
                    vm.$refs.tree.setCheckedKeys([]);
                }

            }, 100)


        },
        get_menu_list() {
            let vm = this, url = '/admin/role/add_role';
            this.apiGet(url).then((res) => {
                if (res.code) {
                    vm.menu_list = res.data.menu;
                } else {
                    vm.handleError(res)
                }
            })
        },
        save_menu(data) {
            // this.dialogFormVisible = false 
            let _data = this.$refs.tree.getCheckedKeys(true);
            console.log(_data)
            this.dialog.menu_auth = _data.join(',');
            let vm = this, url = '/admin/role/save_role';
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
        }

    },
    //组件初始化
    created() {
        this.get_list();
        this.setBreadcrumb(['系统', '角色管理'])
        this.setMenu('4-0');
        this.get_menu_list()
    }

}
</script>
