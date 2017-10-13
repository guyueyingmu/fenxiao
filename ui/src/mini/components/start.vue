<template>
    <div>
        <div class="rate-warp">
            <div class="rate-list">
                <div class="li">
                    <ul class="thumb-list">
                        <li>
                            <img src="item.good_img" width="40" height="40">
                            <div class="info">
                                <div class="title">item.good_title</div>

                            </div>
                        </li>
                    </ul>
                    <div class="m">
                        商品评分：
                        <rate :value="stars" :text="true" :size="26"></rate>
                        <textarea class="ui-input" style="min-height:80px;" placeholder="请输入您的内容" maxlength="200"></textarea>

                        <div class="thumb-rate">
                            <li v-for="item in files" :key="item.id">
                                <img :src="item" width="100%" height="100%">
                            </li>
                            <li v-if="files.length < 4">
                                <vue-file-upload label="上传" icon="iconfont icon-tupian" :max="4" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>
                            </li>
                        </div>

                    </div>
                </div>
                <div class="li">
                    <ul class="thumb-list">
                        <li>
                            <img src="item.good_img" width="40" height="40">
                            <div class="info">
                                <div class="title">item.good_title</div>

                            </div>
                        </li>
                    </ul>
                    <div class="m">
                        商品评分：
                        <rate :value="stars" :text="true" :size="26"></rate>
                        <textarea class="ui-input" style="min-height:120px;" placeholder="请输入您的内容" maxlength="200"></textarea>

                        <div class="thumb-rate">
                            <li v-for="item in files" :key="item.id">
                                <img :src="item">
                            </li>
                             <li  v-if="files.length < 4">
                                <vue-file-upload label="上传" icon="iconfont icon-tupian" :max="4" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding-top:15px;">
                <button class="ui-btn ui-btn-block" type="button">发表</button>

            </div>
        </div>
    </div>
</template>
<script>
import http from '@/assets/js/http'
import Rate from './rate'
import VueFileUpload from 'vue-file-upload';
export default {
    name: 'start',
    mixins: [http],
    components: {
        Rate,
        VueFileUpload
    },
    data() {
        return {
            reqopts: {
                formData: {
                    image_type: 'message_img'
                },
                responseType: 'json',
                withCredentials: false
            },
            files: [
                {},
                {},
                {}
            ],
            filters: [
                {
                    name: "imageFilter",
                    fn(file) {
                        var type = '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                        return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
                    }
                }
            ],
            stars: 0,
            cbEvents: {
                onCompleteUpload: (file, res, status, header) => {
                    let vm = this;

                    if (file.isSuccess) {

                        if (res.code) {
                            vm.files.push(res.data.img_path)
                        } else {

                            file.cancel();
                            file.remove();
                            this.$alert(res.msg)
                        }


                    }

                }
            },
        }
    },
    methods: {

        onStatus(file) {
            if (file.isSuccess) {
                return "上传成功";
            } else if (file.isError) {
                return "上传失败";
            } else if (file.isUploading) {
                return "正在上传";
            } else {
                return "待上传";
            }
        },
        onAddItem(files) {
            console.log(files);
            this.files = files;
        },
        uploadItem(file) {
            //单个文件上传
            file.upload();
        },
        get_list(){
            let id = this.$route.params.order_id;
            if(!id){
                this.$msg('没有ID')
                return;
            }
            console.log(id)
            let vm= this,url='/mini/Comment/get_list?good_id='+id,data={};
            this.apiGet(url,data).then(res=>{
                if(res.code){
                    vm.list = res.list
                    vm.pages = res.pages

                }else{
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.get_list();
        this.setTitle('发表评论')


    }
}
</script>
