<template>
    <div>
        <div v-if=" !isOk" class="rate-warp">
            <div class="rate-list">
                <div class="li" v-for="(item,idx) in list" :key="idx">
                    <ul class="thumb-list">
                        <li>
                            <img v-lazy="item.good_img" width="40" height="40">
                            <div class="info">
                                <div class="title">{{item.good_title}}</div>

                            </div>
                        </li>
                    </ul>
                    <div class="m">
                        商品评分：
                        <rate v-model="item.stars" :text="true" :size="26"></rate>
                        <textarea class="ui-input" style="min-height:80px;" v-model="item.content" placeholder="请输入您的内容" maxlength="200"></textarea>

                        <div class="thumb-rate">
                            <li v-for="img in item.imgs" :key="img">
                                <img :src="img" width="100%" height="100%">
                            </li>
                            <li v-if="files.length < 4">
                                <vue-file-upload label="上传" @onAdd="onAddItem(idx)" :imgIdx="idx" icon="iconfont icon-tupian" :max="4" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>
                            </li>
                        </div>

                    </div>
                </div>

            </div>
            <div style="padding-top:15px;">
                <button class="ui-btn ui-btn-block" type="button" @click="add">发表</button>

            </div>
        </div>
        <div v-else  class="paySuccess">
             <i class="iconfont icon-xuanze"></i>
            <div class="other">感谢您的评论</div>
               <button type="button" class="ui-btn" @click="goto('/')">返回首页</button>
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
            isOk:false,
            list: [],
            imgIdx:0,
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
                onCompleteUpload: (file, res, status, header) => {
                    let vm = this;
         

                    if (file.isSuccess) {

                        if (res.code) {
                             let _item = vm.list[vm.imgIdx];
                        
                             _item.imgs.push(res.data.img_path)
                             vm.list.splice(vm.imgIdx,1,_item)
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
        onAddItem(idx) {
           this.imgIdx = idx
          
        },
        get_list() {
            let id = this.$route.params.order_id;
            if (!id) {
                this.$msg('没有ID')
                return;
            }
            let vm = this, url = '/mini/Comment/get_order_goods?order_id=' + id, data = {};
            this.apiGet(url, data).then(res => {
                if (res.code) {
                    let _list = res.data;
                    for (let _i of res.data) {
                        _i.content = "";
                        _i.stars = 0;
                        _i.imgs = [];
                    }

                    vm.list = _list
                    vm.pages = res.pages

                } else {
                    vm.handleError(res)
                }
            })
        },
        add() {
            let order_id = this.$route.params.order_id;
            if (!order_id) {
                this.$msg('没有order_id')
                return;
            }
            let _list = JSON.stringify(this.list)
            let vm = this, url = '/mini/Comment/add', data = {
                order_id:order_id,
                good_info:_list
            };
            this.apiPost(url, data).then(res => {
                if (res.code) {
                    vm.$msg(res.msg)
                    vm.isOk = true;

                } else {
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
