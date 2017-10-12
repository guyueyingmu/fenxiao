<template>
    <div class="talk">
        <div  v-loading.win="serverInit"></div>
        <div class="reply_list_box" :style="{'top':$is.WeiXin == false?'68px':'10px'}">
            <div class="reply_list_content" id="reply_list_content">
                <div class="sd-scroller" >
                    <scroller ref="ss">
                        <div class="item" v-for="(item,idx) in list" :key="idx">
                            <div class="item-box" :class="{'self':item.send_user == 1}">
                                <div class="avt"><img src="static/mini/img/demo/avt.jpg" width="40" height="40">
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
                        <button type="button" class="ui-btn ui-btn-send" @click="onSend(1, info.content)">发送</button>
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

        //新增商品消息
        this.onSend(3, this.$route.params.good_id);
        setTimeout(function(){
            vm.get_list();
        },500)
        
    },
    data() {
        return {
            serverInit:true,
            reqopts: {
                formData: {
                    image_type: 'message_img'
                },
                responseType: 'json',
                withCredentials: false
            },
            list: [
                // { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                // { send_user: 2, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                // { send_user: 2, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2,add_time:"2012-12-12 18:12:12" },
                // { send_user: 1, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 1, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 1, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 2, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 2, user_name: '赵多', content: { img_url: 'static/mini/img/demo/1.png', thumb_img_url: 'static/mini/img/demo/1.png' }, type: 2 },
                // { send_user: 2, user_name: '赵多', content: { good_img: 'static/mini/img/demo/1.png', good_title: '维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售', good_price: "299.00" }, type: 3 },
                // { send_user: 1, user_name: '赵多', content: { good_img: 'static/mini/img/demo/1.png', good_title: '维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售', good_price: "299.00" }, type: 3 },
                // { send_user: 1, user_name: '李明', content: '你好职b', type: 1 },
                // { send_user: 2, user_name: '李明', content: '你好职b', type: 1 },
            ],
            list_pages: {},
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
                this.onSend(2, res.data.img_path);
            }
        },
        //发送消息
        onSend(type, content) {
            let url = '/mini/Message/send_msg', vm = this, data = {type: type, content: content};
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    if(type != 3){
                        vm.$msg(res.msg);
                    }      
                    vm.info.content = '';              
                } else {
                    vm.handleError(res)
                }
            })
        },
        get_list(page){
            page = page || 1;
            let url = '/mini/Message/chat_list?page=' + page,
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.list_pages = res.data.pages;
                    if (page < 2) {
                        vm.list = res.data.list;
                    } else {
                        let _list = vm.list, _new_list = res.data.list;
                        _list = _new_list.concat(_list);
                        vm.list = _list;
                    }
                 
                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
      const  ws = new WebSocket("ws://127.0.0.1:8282");
      let vm = this;
        ws.onmessage = function(e) {
            // json数据转换成js对象
            var data = eval("(" + e.data + ")");
            var type = data.type || '';
            switch (type) {
                // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
                case 'init':
                    // console.log(data);
                    // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
                    //                $.post('./bind.php', {client_id: data.client_id}, function(data){}, 'json');
                    let url = '/mini/Message/bind'
                    vm.apiPost(url,data).then(res=>{
                        console.log(res)
                        if(res.code){
                            vm.serverInit = false
                        }
                    })
                    break;
                case 'msg':
                    vm.list.push(data.content);
                    break;
                // 当mvc框架调用GatewayClient发消息时直接alert出来
                default:
                //                alert(e.data);
            }
        };
    }


}

</script>
