<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="商品名/编号">
                    <el-input v-model="formInline.keyword" placeholder="商品名/编号" style="width:120px"></el-input>
                </el-form-item>

                <el-form-item label="商品分类">
                    <el-select v-model="formInline.cat_id" placeholder="商品分类" style="width:120px" clearable>

                        <el-option v-for="item in $store.state.cat_list" :key="item.id" :value="item.id" :label="item.cat_name"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="商品类型">
                    <el-select v-model="formInline.good_type" placeholder="商品类型" style="width:120px" clearable>
                        <el-option v-for="item in $store.state.GOODTYPE" :key="item.id" :value="item.id" :label="item.label"></el-option>
                    </el-select>
                </el-form-item>



                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>
         

        </div>

        <el-table :data="list"  border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="商品编号" width="100" ></el-table-column>
            <el-table-column prop="good_title" label="商品标题" width="150" ></el-table-column>
            <el-table-column prop="cat_name" label="商品分类" width="150"></el-table-column>
            <el-table-column prop="specification" label="商品规格" width="150"></el-table-column>
            <el-table-column prop="brand" label="品牌" width="150"></el-table-column>
            <el-table-column prop="credits" label="积分兑换" width="150"></el-table-column>
            <el-table-column prop="presenter_credits" label="赠送积分" width="150"></el-table-column>
            <el-table-column prop="good_type" label="商品类型" width="250">
                 <template scope="scope">
                  <span style="font-size:12px;">{{getType(scope.row.good_type)}}</span>
                </template>
            </el-table-column>
            <el-table-column prop="distribution" label="参与分销" width="100">
                <template scope="scope">
                    {{scope.row.distribution == 1?'参与':'不参与'}}
                </template>
            </el-table-column>
            <el-table-column prop="status" align="center" label="是否上架" width="150">
                <template scope="scope">
                    {{scope.row.status == 1?'上架':'下架'}}
                </template>
            </el-table-column>
            <el-table-column prop="sort" label="排序" width="100"></el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="180"></el-table-column>

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
            let url = '/admin/goodsall/get_list?page=' + page,
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
            let url = '/admin/goodsall/del/good_id/' + _data.id,
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
        this.setBreadcrumb(['分销', '分销商品列表'])
        
    }

}
</script>
