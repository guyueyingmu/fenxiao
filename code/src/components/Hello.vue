<template>
    <div>
        <el-form :inline="true" :model="formInline" class="demo-form-inline">
            <el-form-item label="审批人">
                <el-input v-model="formInline.user" placeholder="审批人"></el-input>
            </el-form-item>
            <el-form-item label="活动区域">
                <el-select v-model="formInline.region" placeholder="活动区域">
                    <el-option label="区域一" value="shanghai"></el-option>
                    <el-option label="区域二" value="beijing"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit">查询</el-button>

            </el-form-item>
        </el-form>
        <el-popover ref="popover5" placement="top" width="160" v-model="visible2">
            <p>这是一段内容这是一段内容确定删除吗？</p>
            <div style="text-align: right; margin: 0">
                <el-button size="mini" type="text" @click="visible2 = false">取消</el-button>
                <el-button type="primary" size="mini" @click="delOk">确定</el-button>
            </div>
        </el-popover>

        <el-button v-popover:popover5>删除</el-button>
        
    </div>
</template>

<script>
export default {
    data() {
        return {
            visible2: false,
            formInline: {
                user: '',
                region: ''
            }
        }
    },
    methods: {
        delOk() {
            this.$message({
                message: '恭喜你，删除成功',
                type: 'success'
            });
            this.visible2 = false
        },
        onSubmit() {
            console.log('submit!');
            this.$message('这是一条消息提示');
        }
    }
}
</script>
