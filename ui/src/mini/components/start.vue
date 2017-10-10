<template>
    <div style="padding:15px">
        <div class="rate-warp">
            <div style="text-align: center;padding:15px">
                <rate :value="stars" :text="true" :size="30"></rate>

            </div>
            <div>
                <textarea class="ui-input" style="min-height:120px;" placeholder="请输入您的内容" maxlength="200"></textarea>
            </div>
            <div class="thumb-rate">
                <li v-for="item in files">
                    <img :src="item">
                </li>
            </div>
            <div>
                <vue-file-upload label="上传" icon="iconfont icon-tupian" :max="4" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>

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
            files: [],
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
                onCompleteUpload: (file, response, status, header) => {
                    let vm = this;
                    if (file.isSuccess) {
                   
                        if(response.code){
                            
                            vm.files.push(response.data.img_path)
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
    },
    created() {
        this.setTitle('发表评论')


    }
}
</script>
