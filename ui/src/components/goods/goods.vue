<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="商品名/编号">
                    <el-input v-model="formInline.goods_name" placeholder="商品名/编号" style="width:120px"></el-input>
                </el-form-item>

                <el-form-item label="商品分类">
                    <el-select v-model="formInline.cat_id" placeholder="商品分类" style="width:120px">
                        <el-option label="区域一" :value="1"></el-option>
                        <el-option label="区域二" :value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="商品类型">
                    <el-select v-model="formInline.goods_type" placeholder="商品类型" style="width:120px">
                        <el-option label="区域一" :value="1"></el-option>
                        <el-option label="区域二" :value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="商品状态">
                    <el-select v-model="formInline.status" placeholder="商品状态" style="width:120px">
                        <el-option label="上架" :value="1"></el-option>
                        <el-option label="下架" :value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSubmit">搜索</el-button>
                </el-form-item>
            </el-form>
            <el-button type="warning" class="goods_add_btn" @click="goto('goods_add')">添加商品</el-button>

        </div>

        <el-table :data="list" stripe style="width: 100%" v-loading.body="loading">
            <el-table-column prop="id" label="商品编号" width="100" fixed="left"></el-table-column>
            <el-table-column prop="good_name" label="商品名" width="150" fixed="left"></el-table-column>
            <el-table-column prop="cat_id" label="商品分类" width="150"></el-table-column>
            <el-table-column prop="specification" label="商品规格" width="150"></el-table-column>
            <el-table-column prop="brand" label="品牌" width="150"></el-table-column>
            <el-table-column prop="price" label="销售价格" width="150"></el-table-column>
            <el-table-column prop="credits" label="积分兑换" width="150"></el-table-column>
            <el-table-column prop="presenter_credits" label="赠送积分" width="150"></el-table-column>
            <el-table-column prop="good_type" label="商品类型" width="150"></el-table-column>
            <el-table-column prop="distribution" label="参与分销" width="150"></el-table-column>
            <el-table-column prop="status" align="center" label="是否上架" width="150">
                <template scope="scope">
                    {{scope.row.status === 1?'上架':'下架'}}
                </template>
            </el-table-column>
            <el-table-column prop="sort" label="排序" width="100"></el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="180"></el-table-column>
            <el-table-column label="操作" width="120" fixed="right"  align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="goto('goods_edit/id/'+scope.row.id)">编辑</el-button>
                    <el-button type="text" size="small" >删除</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
import { mapActions } from "vuex";
import http from '@/assets/js/http'
export default {
    mixins: [http],
    data() {
        return {
            formInline: {
                goods_type: '',
                goods_name: '',
                status: ''

            },
            list: []
        }
    },
    methods: {
        ...mapActions({
            setBreadcrumb: 'setBreadcrumb'
        }),
        onSubmit() {

        },
     
        get_list() {
            let url = '/admin/goodsall/get_list',
                vm = this;
            vm.loading = true;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.list = res.data.list;
                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })

        }
    },
    created() {
        this.get_list();
        this.setBreadcrumb(['商品', '商品列表'])
    }

}
</script>
