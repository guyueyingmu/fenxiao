<template>
    <div class="talk">
        <div class="reply_list_box" :style="{'top':$is.WeiXin == false?'68px':'10px'}">
            <div class="reply_list_content" id="reply_list_content">
                <div class="sd-scroller" v-loading="loading">
                    <scroller ref="ss">
                        <div class="item" v-for="(item,idx) in list" :key="idx">
                            <div class="item-box" :class="{'self':item.send_user == 2}">
                                <div class="avt"><img src="static/app/demo/avt.jpg" width="40" height="40">
                                    <span class="name">{{item.user_name}}</span>
                                </div>
                                <div class="item-content">
                                    <span v-if="item.type ===1">{{item.content}}</span>

                                    <span v-if="item.type ===2" @click="onZoom(item.content.img_url)"><img :src="item.content.thumb_img_url" width="60" height="60"></span>

                                    <div class="pro_box" v-if="item.type ===3">
                                        <div class="pro_box_flex">
                                            <div class="pro_img"><img :src="item.content.good_img" width="40" height="40"></div>
                                            <div class="info">
                                                <div class="bl">{{item.content.good_title}}</div>
                                                <div class="cost">￥{{item.content.good_price}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="time">{{item.add_time}}</div>
                        </div>
                    </scroller>
                </div>

            </div>
            <div class="sendBox">
                <div class="flex">
                    <div class="upload">
                        <vue-file-upload label="上传" icon="iconfont icon-tupian" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>
                    </div>
                    <div class="input">
                        <input type="text" class="ui-input" id="input-key" autocomplete="off" placeholder="请输入内容" v-model="info.content"> </input>
                    </div>
                    <div style="width:60px;margin-left:10px;">
                        <button type="button" class="ui-btn ui-btn-send" @click="onSend()">发送</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>

import http from '@/assets/js/http'
import VueFileUpload from 'vue-file-upload'
export default {
    name: 'class',
    mixins: [http],
    components: {
        VueFileUpload
    },
    mounted() {
        this.setTitle('在线客服')
        let vm = this;
        setTimeout(function() {
            var input = document.getElementById('input-key');
            input.addEventListener('focus', function() {
                  vm.$refs['ss'].scrollTo(0, 999, 0);
                setTimeout(function() {
                  
                    document.body.scrollTop = 99999
                }, 200)
            }, false)
        }, 100)
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
            list: [
                { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                { send_user: 2, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                { send_user: 2, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 1, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 1, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 1, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 2, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 2, user_name: '赵多', content: { img_url: 'static/app/demo/1.png', thumb_img_url: 'static/app/demo/1.png' }, type: 2 },
                { send_user: 2, user_name: '赵多', content: { good_img: 'static/app/demo/1.png', good_title: '维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售', good_price: "299.00" }, type: 3 },
                { send_user: 1, user_name: '赵多', content: { good_img: 'static/app/demo/1.png', good_title: '维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售', good_price: "299.00" }, type: 3 },
                { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                { send_user: 2, user_name: '李明', content: '你好职b', type: 1 },
            ],
            info: {
                content: ''
            },
            cbEvents: {
                onCompleteUpload: (file, response, status, header) => {
                    if (file.isSuccess) {
                        this.success(response)

                    }

                }
            },
        }
    },
    methods: {
        onZoom(url) {
            console.log(url)
        },
        success(res) {
            if (res.code) {
                let _d = {};
                _d.user_id = this.info.user_id
                _d.type = 2;
                _d.send_user = 2;
                _d.user_name = this.info.nickname
                _d.content = res.data.img_path
                _d.content2 = {}
                _d.content2 = {
                    thumb_img_url: res.data.img_path,
                    img_url: res.data.big_img_path
                }
                //  this.send(_d)
            }
        },
        onSend() {

        }

    }


}

</script>
