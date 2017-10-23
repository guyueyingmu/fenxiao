<template>
    <div class="talk">
        <div v-loading.win="serverInit"></div>
        <div class="reply_list_box" :style="{'top':$is.WeiXin == false?'58px':'5px'}">
            <div class="reply_list_content" id="reply_list_content">
                <div class="sd-scroller">
                    <scroller ref="ss" :onRefresh="refresh" refreshText="加载更多">
                        <div class="item" v-for="(item,idx) in list" :key="idx">
                            <div class="time">{{item.add_time}}</div>
                            <div class="item-box" :class="{'self':item.send_user == 1}">
                                <div class="avt"><img v-lazy="head_img" width="40" height="40">
                                    <span class="name">{{item.user_name}}</span>
                                </div>
                                <div class="item-content">
                                    <span v-if="item.type == 1">{{item.content}}</span>

                                    <span v-if="item.type == 2" @click="onZoom(item.content.img_url)"><img v-lazy="item.content.thumb_img_url" width="60" height="60"></span>

                                    <div class="pro_box" v-if="item.type == 3">
                                        <div class="pro_box_flex" @click="goto('/detail/id/'+item.content.good_id)">
                                            <div class="pro_img"><img v-lazy="item.content.good_img" width="40" height="40"></div>
                                            <div class="info">
                                                <div class="bl">{{item.content.good_title}}</div>
                                                <div class="cost">￥{{item.content.good_price}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </scroller>
                </div>

            </div>
            <div class="sendBox">
                <div class="flex">
                    <div class="upload">
                        <vue-file-upload label="" icon="iconfont icon-tupian" :request-options="reqopts" :autoUpload="true" :events='cbEvents' name="image" url="/admin/Asset/upload?_ajax=1"></vue-file-upload>
                    </div>
                    <div class="input">
                        <input  type="text" class="ui-input needsclick" id="input-key" autocomplete="off" placeholder="请输入内容" v-model="info.content">  </input>
                      
                    </div>
                    <div  class="send_btn_warp">
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
                setTimeout(function() {
                    document.body.scrollTop = 99999;
                     vm.$refs['ss'].scrollTo(0, 99999, 0);
                }, 380)
                   
            }, false)
        }, 1000)

        //新增商品消息
        this.onSend(3, this.$route.params.good_id);
        setTimeout(function() {
            vm.get_list();
        }, 500)

    },
    data() {
        return {
            serverInit: true,
            reqopts: {
                formData: {
                    image_type: 'message_img'
                },
                responseType: 'json',
                withCredentials: false
            },
            list: [],
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
        refresh(e) {
            console.log('刷新')
            let vm = this;
            if (this.list_pages.total_page > this.list_pages.current_page) {
                setTimeout(() => {
                    vm.$refs['ss'].finishPullToRefresh()
                    vm.get_list(parseInt(vm.list_pages.current_page, 10) + 1)
                }, 1000)
            }else{
                 vm.$refs['ss'].finishPullToRefresh()
            }

        },
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
            let url = '/mini/Message/send_msg', vm = this, data = { type: type, content: content };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    if (type != 3) {
                        // vm.$msg(res.msg);
                    }
                    vm.info.content = '';
                } else {
                    vm.handleError(res)
                }
            })
        },
        get_list(page) {
            page = page || 1;
            let url = '/mini/Message/chat_list?page=' + page,
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.list_pages = res.data.pages;
                    if (page < 2) {
                        vm.list = res.data.list;
                        setTimeout(() => {
                            vm.$refs['ss'].scrollTo(0, 999, 0);
                        }, 200)
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
        const ip = window.location.hostname;//'119.23.75.94';//
        const ws = new WebSocket("ws://" + ip + ":8282");

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
                    vm.apiPost(url, data).then(res => {

                        if (res.code) {
                            vm.serverInit = false
                            window.bb = vm.$refs['ss'];
                        }
                    })
                    break;
                case 'msg':
                    vm.list.push(data.content);
                    setTimeout(() => {
                        if (vm.$refs['ss']) {
                            vm.$refs['ss'].scrollTo(0, 9999, 20);
                        }

                    }, 200)
                    break;
                // 当mvc框架调用GatewayClient发消息时直接alert出来
                default:
                //                alert(e.data);
            }
        };
    }


}

</script>
