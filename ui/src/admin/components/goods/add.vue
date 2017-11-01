

<template>
    <div style="padding:1em 10% 4em 4em" v-loading="loading" element-loading-text="拼命加载中">
        <el-form :model="form" ref="form" label-width="100px">
            <el-form-item label="商品小图" class="my_error is-required">
                <div class="red small">尺寸为 280 * 280 正方形</div>
                <el-upload class="avatar-uploader" action="/admin/Asset/upload?_ajax=1" name="image" :data="{img_type:`good_img`}" accept="image/jpeg,image/png" :show-file-list="false" :on-success="hSuccess" :before-upload="beforeAvatarUpload">
                    <img v-if="form.good_img" :src="form.good_img" class="avatar">
                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
            </el-form-item>

            <el-form-item label="商品轮播图" class="my_error is-required">
                <div class="red small">尺寸为 540 * 540 正方形</div>
                <el-upload list-type="picture-card" action="/admin/Asset/upload?_ajax=1" name="image" :data="{img_type:`good_banner_img`}" accept="image/jpeg,image/png" :file-list="bannerImg_temp_list" :on-success="pSuccess" :on-preview="handlePV" :on-remove="handleRemove">
                    <i class="el-icon-plus"></i>
                </el-upload>
                <el-dialog v-model="dialogVisible" size="tiny">
                    <img width="100%" :src="dialogImageUrl" alt="">
                </el-dialog>
            </el-form-item>

            <el-row>
                <el-col :span="8">
                    <el-form-item label="品牌">
                        <el-input v-model="form.brand" placeholder="品牌" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>

                <el-col :span="8">
                    <el-form-item label="商品编号">
                        <el-input v-model="form.good_num" placeholder="商品编号" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="8">
                    <el-form-item label="商品名称"  class="is-required">
                        <el-input v-model="form.good_name" placeholder="商品名称" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">

                    <el-form-item label="商品分类"  class="is-required">
                        <el-tooltip class="item" effect="dark" content="亲~ 分类可以在左边主菜单里进行增加哦! ~_~" placement="right">
                            <el-select placeholder="商品分类" v-model="form.cat_id" style="display:block;">
                                <el-option v-for="item in $store.state.cat_list" :key="item.id" :value="item.id" :label="item.cat_name"></el-option>
                            </el-select>
                        </el-tooltip>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="8">
                    <el-form-item label="供应商">
                        <el-input v-model="form.supplier" placeholder="供应商" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="商品颜色">
                        <el-input v-model="form.color" placeholder="商品颜色" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>
                <el-col :span="8">
                    <el-form-item label="商品规格">
                        <el-input v-model="form.specification" placeholder="商品规格" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="销售价格"  class="is-required">
                        <el-input v-model="form.price" placeholder="销售价格" :maxlength="20"></el-input>
                    </el-form-item>

                </el-col>
            </el-row>

            <el-row>
                <el-col :span="8">
                    <el-form-item label="商品类型"  class="is-required">
                        <el-select placeholder="商品类型" v-model="form.good_type" style="display:block;">
                            <el-option v-for="(item,k) in $store.state.GOODTYPE" :key="item.id" :value="item.id" :label="item.label">
                                <span style="float: left">{{item.label}}</span>
                                <span style="float: right;opacity:0.7">{{item.tip}}</span>
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="8">

                    <el-form-item label="参与分销" v-if="parseInt(form.good_type,10) < 4" class="is-required">
                        <el-radio-group v-model="form.distribution" placeholder="积分类商品不参与分销，参与分销的必须是现金支付的商品">
                            <el-radio :label="`1`">参与</el-radio>
                            <el-radio :label="`2`">不参与</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="积分兑换" v-if="parseInt(form.good_type,10) >= 4"  class="is-required">
                        <el-input v-model="form.credits" placeholder="积分类商品时必填" :maxlength="7"></el-input>
                    </el-form-item>

                </el-col>
            </el-row>
            <el-row>
                <el-col :span="8">
                    <el-form-item label="可用库存">
                        <el-input v-model="form.inventory" placeholder="可用库存" :maxlength="7"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="赠送积分">
                        <el-input v-model="form.presenter_credits" placeholder="购买该商品赠送积分" :maxlength="7"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-row>

                <el-col :span="8">
                    <el-form-item label="排序">
                        <el-input v-model="form.sort" placeholder="请输入0~999之间整数，数字越大排序越前" :maxlength="3"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="是否上架">
                        <el-radio-group v-model="form.status" placeholder="是否上架">
                            <el-radio :label="`1`">上架</el-radio>
                            <el-radio :label="`2`">下架</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="商品标题" class="is-required">
                <el-input v-model="form.good_title" placeholder="商品标题" :maxlength="100"></el-input>
            </el-form-item>
            <el-form-item label="商品详情" class="is-required">
                <!-- <el-input type="textarea" v-model="form.detail" placeholder="商品详情" :maxlength="100"></el-input> -->
                <!-- <vue-editor v-model="form.detail"></vue-editor> -->
                <!-- <UE :defaultMsg=defaultMsg :config=config ref="ue"></UE> -->
                <vue-html5-editor :z-index="1000" :auto-height="true"  @change="updateContent" :content="form.detail" :height="500" placeholder="商品详情"></vue-html5-editor>
            </el-form-item>
            <div style="height:40px;"></div>
            <el-form-item label-width="40%">
                <el-button type="primary" @click="submitForm('form')">{{isEdit?'保存编缉':'立即创建'}}</el-button>
                <el-button @click="resetForm('form')">重置</el-button>
            </el-form-item>
        </el-form>

    </div>
</template>
<script>


import http from '../../assets/js/http'
import options from '../../assets/js/editconfig'
import VueHtml5Editor from 'vue-html5-editor'

const editor = new VueHtml5Editor(options)
export default {
    mixins: [http],
    components: {
        "vue-html5-editor": editor
    },
    data() {
        return {
            isEdit: false,
            dialogVisible: false,
            dialogImageUrl: '',
            bannerImg_temp_list: [],
            cat_list: [],
            form: {},

        };
    },
    watch: {
        $route: "initData"
    },
    created() {
        // 组件创建完后获取数据，
        this.initData();
        window.$vm = this

    },
    methods: {
        //组件内的方法
        updateContent(v){
       
            this.form.detail =v 
        },

        //数据初始化
        initData() {
            let vm = this, _form = {
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
                distribution: '1',
                credits: '',
                status: '1',
                inventory: '',
                presenter_credits: '',
                sort: 0,
                good_title: '',
                detail: ''
            }
            this.form = _form;
            let _breadCrumb = ['商品', '增加商品'];
            if (vm.$route.name == 'Goods_edit') {
                _breadCrumb = ['商品', '编辑商品'];
                vm.isEdit = true;
                vm.get_edit_item(vm.$route.params.id);
            } else {

            }
            vm.setBreadcrumb(_breadCrumb)
        },

        //删除轮播图
        handleRemove(file, fileList) {
            let _d = this.form.banner_img;
            let url = file.url;
            for (let i = 0; i < _d.length; i++) {
                if (url == _d[i].img_url) {
                    _d[i].is_show = 2;
                    this.bannerImg_temp_list.splice(i, 1)
                    break
                }
            }
            this.form.banner_img = _d;


        },

        //轮播图上传成功
        pSuccess(res, fileList) {
            if (res.code == 1) {
                this.bannerImg_temp_list.push({ url: res.data.img_path })
                let _d = { id: '', img_url: res.data.img_path, is_show: 1 }
                this.form.banner_img.push(_d)
            }
        },
        //查看轮播图
        handlePV(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;

        },

        //小图上传成功
        hSuccess(res, file) {
            if (res.code) {
                this.form.good_img = res.data.img_path
            }
        },
        //小图上传前处理
        beforeAvatarUpload(file) {
            const isJPG = file.type == 'image/jpeg';
            const isPNG = file.type == 'image/png';
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

        //提交表单
        submitForm(formName) {
            this.loading = true;
            let url = this.isEdit ? '/admin/goodsall/edit?good_id=' + this.form.id : '/admin/goodsall/add',
                data = this.form,
                vm = this;
            this.apiPost(url, data).then(function(res) {
                                vm.loading = false;
                if (res.code) {
                    vm.$message.success(res.msg);
                    setTimeout(function() {
                        vm.$router.push('/goods');
                    }, 500)

                } else {
                    vm.handleError(res)
                }
      

            })
        },
        //重置表单
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },


        //编辑时获取单条数据
        get_edit_item(id) {
            this.loading = true;
            let url = '/admin/goodsall/get_good_info?good_id=' + id,
                vm = this;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                  
                    vm.bannerImg_temp_list = [];
                    let _d = res.data.banner_img;
                    for (let i = 0; i < _d.length; i++) {
                        if (_d[i].is_show == 1) {
                            vm.bannerImg_temp_list.push({ url: _d[i].img_url })
                        }

                    }
                    let _item = res.data;
                    _item.cat_id = _item.cat_id.toString()
                    _item.good_type = _item.good_type.toString()
                    _item.credits = _item.credits.toString()
                    _item.distribution = _item.distribution.toString()
                    _item.inventory = _item.inventory.toString()
                    _item.presenter_credits = _item.presenter_credits.toString()
                    _item.status = _item.status.toString()
                    // }
                    vm.form = res.data;
                    vm.loading = false;
                } else {
                    vm.$alert(res.msg, '警告', {
                        type: 'error',
                        callback: function() {
                            vm.$router.back();
                        }

                    });

                }
            })
        }

    },

}
</script>
<style scoped>
    @import url(https://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css);
</style>

