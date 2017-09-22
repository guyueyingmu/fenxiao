<template>
    <div>
        <div class="page_heade" @keyup.enter="onSearch()">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="商品分类">
                    <el-input v-model="formInline.keyword" placeholder="商品分类" style="width:220px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
            <el-button type="warning" class="goods_add_btn" @click="open_addCat(false)">添加分类</el-button>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="ID" width="100"></el-table-column>
            <el-table-column prop="cat_img" label="分类小图"  width="100">
                <template scope="scope">
                    <div style="padding:10px 0;">
                        <img :src="scope.row.cat_img"  width="40" height="40" alt="">
                    </div>
                </template>
            </el-table-column>
            <el-table-column prop="cat_name" label="商品分类"></el-table-column>
            <el-table-column prop="sort" label="排序" width="80"></el-table-column>
            <el-table-column prop="nickname" label="添加人" width="200"></el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="200"></el-table-column>
            <el-table-column label="操作" width="120" align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="open_addCat(true,scope.row)">编辑</el-button>
                    <el-button type="text" size="small" @click="onRemove(scope.$index)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <!-- 添加管理员 -->
        <el-dialog title="添加分类" :visible.sync="dialogFormVisible">
            <el-form label-width="100px">
                <el-form-item label="分类名">
                    <el-input v-model="dialog.cat_name" auto-complete="off"></el-input>
                </el-form-item>

                <el-form-item label="排序">
                    <el-input v-model="dialog.sort" placeholder="排序 0 - 999" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="分类小图" class="my_error">
                    <div class="red small">尺寸为 100 * 100 正方形</div>
                    <el-upload class="avatar-uploader" action="/admin/Asset/upload?_ajax=1" name="image" :data="{img_type:`good_cat_img`}" accept="image/jpeg,image/png" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
                        <img v-if="dialog.cat_img" :src="dialog.cat_img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="postNewCat">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import { Upload } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-upload": Upload,
    },
    data() {
        return {
            dialogFormVisible: false,
            isSearch: false,
            formInline: {
                good_type: '',
                keyword: '',
                cat_id: '',
                status: ''
            },
            dialog: {
                cat_name: '',
                id: '',
                sort: '0',
                cat_img: ''
            },
            list: []
        }
    },
    methods: {
        //添加分类
        open_addCat(isEdit, data) {
            this.dialogFormVisible = true;
            if (isEdit) {
                this.dialog = data
            } else {
                this.dialog = {
                    cat_name: '',
                    id: '',
                    sort: '0',
                    cat_img: ''
                }

            }
        },
        //小图上传成功
        handleAvatarSuccess(res, file) {
            if (res.code) {
                this.dialog.cat_img = res.data.img_path
            }
        },
        //小图上传前处理
        beforeAvatarUpload(file) {
            const isJPG = file.type === 'image/jpeg';
            const isPNG = file.type === 'image/png';
            let isTypeOk = false;
            const isLt2M = file.size / 1024 / 1024 < 2;
            if (!isLt2M) {
                this.$message.error('上传图片大小不能超过 2MB!');
            }

            if (!isPNG && !isJPG) {
                this.$message.error('上传图片只能是 PNG 或 JPG 格式!');

            } else {
                isTypeOk = true
            }

            return isLt2M && isTypeOk;
        },
        //保存数据
        postNewCat() {
            let data = this.dialog;
            let url = data.id ? '/admin/goodscat/edit' : '/admin/goodscat/add', vm = this;
            this.apiPost(url, data).then((res) => {
                if (res.code) {
                    vm.$message.success(res.msg);
                    vm.get_list();
                    vm.dialogFormVisible = false;

                } else {
                    vm.handleError(res)
                }

            })

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
            let url = '/admin/goodscat/get_list?page=' + page,
                vm = this;

            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.list = res.data.list;
                    if (vm.isSearch == false) {
                        //通知全局商品分类数据
                        vm.setCatList(res.data.list)

                    }
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
                type: 'warning',
                beforeClose(action, instance, done) {
                    if (action == 'confirm') {
                        vm.removeData(index, done)
                    } else {
                        done(); //关闭窗口
                    }
                },

            }).then(() => {


            }).catch(() => {
            });

        },
        //删除
        removeData(index, cb) {
            let _data = this.list[index]
            let url = '/admin/goodscat/del?id=' + _data.id,
                vm = this;
            vm.loading = true;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.list.splice(index, 1)
                    vm.$message({
                        type: 'success',
                        message: res.msg
                    });
                    if (typeof cb == "function") {
                        cb(); //关闭窗口
                    }

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
        this.setBreadcrumb(['商品', '商品分类'])


    }

}
</script>
