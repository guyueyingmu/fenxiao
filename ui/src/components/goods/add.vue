

<template>
    <div style="width:800px;padding:4em">

        <el-form :model="form" ref="form" label-width="100px">
            <el-form-item label="商品小图" prop="good_img">
                <div class="red">尺寸为 320 * 320 正方形</div>
                <el-upload class="avatar-uploader" action="/admin/Goodsall/upload?_ajax=1" name="image" :data="{img_type:`good_img`}" accept="image/jpeg,image/png" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
                    <img v-if="form.good_img" :src="form.good_img" class="avatar">
                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
            </el-form-item>

            <el-form-item label="商品轮播图">
                <div class="red">尺寸为 640 * 320 长方形</div>
                <el-upload list-type="picture-card" action="/admin/Goodsall/upload?_ajax=1" name="image" :data="{img_type:`banner_img`}" accept="image/jpeg,image/png" :file-list="banner_imgFileList" :on-success="handlePictureSuccess" :on-preview="handlePictureCardPreview" :on-remove="handleRemove">
                    <i class="el-icon-plus"></i>
                </el-upload>
                <el-dialog v-model="dialogVisible" size="tiny">
                    <img width="100%" :src="dialogImageUrl" alt="">
                </el-dialog>
            </el-form-item>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="品牌" prop="brand">
                        <el-input v-model="form.brand" placeholder="品牌" :maxlength="12"></el-input>
                    </el-form-item>
                </el-col>

                <el-col :span="12">
                    <el-form-item label="商品编号" prop="good_num">
                        <el-input v-model="form.good_num" placeholder="商品编号" :maxlength="12"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="商品名称" prop="good_name">
                        <el-input v-model="form.good_name" placeholder="商品名称" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="商品分类">
                        <el-select placeholder="商品分类" v-model="form.cat_id" style="display:block;">
                            <el-option v-for="item in cat_list" :key="item.id" :value="item.id" :label="item.cat_name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="供应商">
                        <el-input v-model="form.supplier" placeholder="供应商" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="商品颜色">
                        <el-input v-model="form.color" placeholder="商品颜色" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="商品规格">
                        <el-input v-model="form.specification" placeholder="商品规格" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="销售价格">
                        <el-input v-model="form.price" placeholder="销售价格" :maxlength="10"></el-input>
                    </el-form-item>

                </el-col>
            </el-row>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="商品类型">
                        <el-select placeholder="商品类型" v-model="form.good_type" style="display:block;">
                            <el-option label="实物类商品（微信支付 需要快递）" :value="1"></el-option>
                            <el-option label="拟类商品（微信支付 无需快递）" :value="2"></el-option>
                            <el-option label="预约类商品（线下支付 无需快递）" :value="3"></el-option>
                            <el-option label="积分实物类商品（积分兑换 需要快递）" :value="4"></el-option>
                            <el-option label="积分虚拟类商品（积分兑换 无需快递））" :value="5"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">

                    <el-form-item label="参与分销" v-if="form.good_type < 4">
                        <el-radio-group v-model="form.distribution" placeholder="积分类商品不参与分销，参与分销的必须是现金支付的商品">
                            <el-radio :label="1">参与</el-radio>
                            <el-radio :label="2">不参与</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="积分兑换" v-if="form.good_type >= 4">
                        <el-input v-model="form.credits" placeholder="积分类商品时必填" :maxlength="10"></el-input>
                    </el-form-item>

                </el-col>
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="可用库存">
                        <el-input v-model="form.inventory" placeholder="可用库存" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="赠送积分">
                        <el-input v-model="form.presenter_credits" placeholder="购买该商品赠送积分" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>

                <el-col :span="12">
                    <el-form-item label="排序">
                        <el-input v-model="form.sort" placeholder="请输入0~999之间整数，数字越大排序越前" :maxlength="3"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="是否上架">
                        <el-radio-group v-model="form.status" placeholder="是否上架">
                            <el-radio :label="1">上架</el-radio>
                            <el-radio :label="2">下架</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="商品标题">
                <el-input v-model="form.good_title" placeholder="商品标题" :maxlength="10"></el-input>
            </el-form-item>
            <el-form-item label="商品详情">
                <!-- <el-input type="textarea" v-model="form.detail" placeholder="商品详情" :maxlength="100"></el-input> -->
                <vue-editor v-model="form.detail" :editorToolbar2="customToolbar"></vue-editor>
            </el-form-item>
            <div style="height:40px;"></div>
            <el-form-item label-width="40%">
                <el-button type="primary" @click="submitForm('form')">立即创建</el-button>
                <el-button @click="resetForm('form')">重置</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import { mapActions } from "vuex";
import http from '../../assets/js/http'
import { VueEditor } from 'vue2-editor'
import { Upload } from 'element-ui'
export default {
    mixins: [http],
    components: {
        "el-upload": Upload,
        VueEditor
    },
    data() {
        return {
            customToolbar: [
                ['bold', 'italic', 'underline','color'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['image']
            ],
            dialogVisible: false,
            dialogImageUrl: '',
            banner_imgFileList: [],
            cat_list: [],
            form: {
                good_img: '',
                banner_img: [],
                brand: '',
                good_num: '',
                good_name: '',
                cat_id: '',
                supplier: '',
                color: '',
                specification: '',
                good_type: '',
                price: '',
                distribution: 1,
                credits: '',
                status: 1,
                inventory: '',
                presenter_credits: '',
                sort: 1,
                good_title: '',
                detail: ''

            },
            rules: {
                name: [
                    { required: true, message: '请输入活动名称', trigger: 'blur' },
                    { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                ],
                region: [
                    { required: true, message: '请选择活动区域', trigger: 'change' }
                ],
                date1: [
                    { type: 'date', required: true, message: '请选择日期', trigger: 'change' }
                ],
                date2: [
                    { type: 'date', required: true, message: '请选择时间', trigger: 'change' }
                ],
                type: [
                    { type: 'array', required: true, message: '请至少选择一个活动性质', trigger: 'change' }
                ],
                resource: [
                    { required: true, message: '请选择活动资源', trigger: 'change' }
                ],
                desc: [
                    { required: true, message: '请填写活动形式', trigger: 'blur' }
                ]
            }
        };
    },
    methods: {
        ...mapActions({
            setBreadcrumb: 'setBreadcrumb'
        }),

        handleRemove(file, fileList) {
            console.log(file, fileList);
        },
        handlePictureSuccess(res, fileList) {
            if (res.code == 1) {
                let _d = { url: res.data.img_path, id: '', img_url: res.data.img_path, is_show: 1 }
                this.banner_imgFileList.push(_d)
                this.form.banner_img.push(_d)
            }


        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;

        },

        handleAvatarSuccess(res, file) {
            if (res.code) {
                this.form.good_img = res.data.img_path
            }
        },
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
        submitForm(formName) {
            let url = '/admin/goodsall/add',
                data = this.form,
                vm = this;
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$message.success('添加成功');

                } else {
                    vm.handleError(res)
                }
                console.log(res)

            })



            // this.$refs[formName].validate((valid) => {
            //     if (valid) {
            //         alert('submit!');
            //     } else {
            //         console.log('error submit!!');
            //         return false;
            //     }
            // });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        get_cat() {
            let url = '/admin/goodscat/get_list',
                vm = this;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.cat_list = res.data.list;
                } else {
                    vm.handleError(res)
                }

            })

        }
    },

    created() {
        this.get_cat();
        this.setBreadcrumb(['商品', '增加商品'])


    }

}
</script>
