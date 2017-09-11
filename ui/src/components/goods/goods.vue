<template>
    <div>
        <div class="page_heade">
            <el-form :inline="true" :model="formInline" class="demo-form-inline">
                <el-form-item label="商品名/编号">
                    <el-input v-model="formInline.goods_name" placeholder="商品名/编号" style="width:120px"></el-input>
                </el-form-item>

                <el-form-item label="商品分类">
                    <el-select v-model="formInline.goods_type" placeholder="商品分类" style="width:120px">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="商品类型">
                    <el-select v-model="formInline.goods_type" placeholder="商品类型" style="width:120px">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="商品状态">
                    <el-select v-model="formInline.goods_status" placeholder="商品状态" style="width:120px">
                        <el-option label="上架" value="0"></el-option>
                        <el-option label="下架" value="1"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSubmit">搜索</el-button>
                </el-form-item>
            </el-form>
            <router-link class="goods_add_btn" to="goods_add">添加商品</router-link>
        </div>

        <el-table :data="list" stripe style="width: 100%">
            <el-table-column prop="date" label="商品编号" width="180"></el-table-column>
            <el-table-column prop="date" label="商品名" width="180"></el-table-column>
            <el-table-column prop="date" label="商品分类" width="180"></el-table-column>
            <el-table-column prop="date" label="商品规格" width="180"></el-table-column>
            <el-table-column prop="date" label="品牌" width="180"></el-table-column>
            <el-table-column prop="date" label="销售价格" width="180"></el-table-column>
            <el-table-column prop="date" label="积分兑换" width="180"></el-table-column>
            <el-table-column prop="date" label="赠送积分" width="180"></el-table-column>
            <el-table-column prop="date" label="商品类型" width="180"></el-table-column>
            <el-table-column prop="date" label="参与分销" width="180"></el-table-column>
            <el-table-column prop="date" label="是否上架" width="180"></el-table-column>
            <el-table-column prop="date" label="排序" width="180"></el-table-column>
            <el-table-column prop="date" label="添加时间" width="180"></el-table-column>
            <el-table-column label="操作" width="180"></el-table-column>
        </el-table>
    </div>
</template>
<script>

export default {
    data() {
        return {
            formInline: {
                goods_type: [],
                goods_name: "",
                goods_status: 0

            },
            list: []
        }
    },
    methods: {
        onSubmit() {

        }
    },
    created() {
        window.Global.setTitle('商品列表')
    }

}
</script>
