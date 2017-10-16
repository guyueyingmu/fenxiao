<template>
    <div>
        <div class="page_heade" @keyup.enter="onSearch()">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="用户呢称">
                    <el-input v-model="formInline.keyword" placeholder="用户呢称" style="width:220px"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSearch()">搜索</el-button>
                    <el-button type="danger" @click="onReset" v-if="isSearch">清空搜索</el-button>
                </el-form-item>
            </el-form>

        </div>

        <el-table :data="list" border style="width: 100%" v-loading.body="loading">
            <el-table-column prop="user_id" label="用户ID" width="100"></el-table-column>
            <el-table-column prop="nickname" label="用户呢称" width="180"></el-table-column>
            <el-table-column prop="content" label="对话内容"></el-table-column>
            <el-table-column prop="read_status" label="是否已读">
                <template scope="scope">
                    {{scope.row.read_status == 1?"未读":"已读"}}
                </template>
            </el-table-column>
            <el-table-column prop="add_time" label="添加时间" width="200"></el-table-column>
            <el-table-column label="操作" width="120" align="center">
                <template scope="scope">
                    <el-button type="text" size="small" @click="open_replyDialog(scope.row)">回复</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination">
            <el-pagination v-if="parseInt(pages.total_page,10) > 1" @current-change="handleCurrentChange" :current-page="parseInt(pages.current_page,10)" :page-size="parseInt(pages.limit,10)" :total="pages.total" layout="total, prev, pager, next,jumper">
            </el-pagination>
        </div>

        <div class="reply_list_main">
            <div v-show="reply_show" class="reply_list_dialog_bg" @click="close_reply"></div>
            <transition name="left">
                <div v-show="reply_show" class="reply_list_dialog">
                    <div class="reply_list_box">
                        <div class="reply_list_content" id="reply_list_content">
                            <div class="sd-scroller" v-loading="replyLoading">

                                <div class="item" v-for="(item,idx) in dialog.list" :key="idx">
                                    <div class="item-box" :class="{'self':item.send_user == 2}">
                                        <div class="avt"><img width="40" height="40">
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
                            </div>

                        </div>
                        <div class="sendBox">
                            <el-row type="flex">
                                <el-col style="width:60px;margin-right:10px;">
                                    <el-upload class="upload-demo" action="/admin/Asset/upload?_ajax=1" name="image" :on-success="messageImgSuccess" :data="{image_type:`message_img`}" :show-file-list="false">
                                        <el-button size="info" type="primary" icon="upload"></el-button>
                                    </el-upload>
                                </el-col>
                                <el-col>
                                    <el-input type="textarea" autosize placeholder="请输入内容" v-model="dialog.info.content"> </el-input>
                                </el-col>
                                <el-col style="width:60px;margin-left:10px;">
                                    <el-button type="info" @click="onSend()">发送</el-button>
                                </el-col>
                            </el-row>

                        </div>
                    </div>
                </div>
            </transition>

        </div>
        <el-dialog v-model="d_zoon" size="tiny">
            <img width="100%" :src="d_z_url" alt="">
        </el-dialog>

    </div>
</template>
<script>
import http from '@/assets/js/http'
import Iscroll from '@/assets/js/iscroll'
export default {
    mixins: [http],

    data() {
        return {
            d_zoon: false,
            d_z_url: '',
            isSearch: false,
            replyLoading: false,
            formInline: {
                keyword: '',

            },
            dialog: {
                info: {
                    content: '',
                    user_id: '',
                },
                list: []

            },
            list: [],
            reply_show: false,
            current_user_id: 0,
            current_client_id: '',
        }
    },
    methods: {
        //回复
        open_replyDialog(item) {
            this.dialog.list = [];
            this.dialog.info = JSON.parse(JSON.stringify(item));
            this.dialog.info.content = '';
            this.reply_show = true;
            this.replyLoading = true;
            document.body.style.overflow = 'hidden';
            this.bind_ws(item.user_id);
            this.current_user_id = item.user_id;
            this.get_talk_list(item.message_group_id);
        },
        bind_ws(user_id){
            const ip = window.location.hostname;
            const  ws = new WebSocket("ws://"+ ip +":8282");
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
                        let url = '/admin/Kefu/bind';
                        vm.current_client_id = data.client_id;
                        vm.apiPost(url,{client_id: data.client_id, user_id: user_id}).then(res=>{
                            console.log(res)
                            if(res.code){
                                // vm.serverInit = false
                            }
                        })
                        break;
                    case 'msg': console.log(1,data.content);
                        vm.dialog.list.push(data.content);
                        setTimeout(() => {
                            IScroll1.refresh();
                        }, 200)
                        setTimeout(() => {
                            IScroll1.scrollTo(0, IScroll1.maxScrollY, 200)
                        }, 300)
                        break;
                    // 当mvc框架调用GatewayClient发消息时直接alert出来
                    default:
                    //                alert(e.data);
                }
            };
        },
        close_reply() {
            this.reply_show = false;
            document.body.style.overflow = 'auto'
            IScroll1.disable();

            let user_id = this.current_user_id;
            this.current_user_id = 0;
            //退出聊天分组
            let url = '/admin/Kefu/leave_group'
            this.apiPost(url,{client_id: this.current_client_id, user_id: user_id}).then(res=>{
                console.log(res)
            })
        },
        //小图上传成功
        messageImgSuccess(res, file) {
            if (res.code) {
                let _d = {};
                _d.message_group_id = this.dialog.info.message_group_id
                _d.type = 2;
                _d.content = res.data.img_path
                this.send(_d)
            }
        },
        //小图上传前处理
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
        //取对话数据
        get_talk_list(message_group_id) {
            let url = '/admin/Kefu/detail?message_group_id=' + message_group_id, vm = this;
            this.apiGet(url).then((res) => {
                if (res.code) {
                    vm.dialog.list = res.data.list;
                    this.replyLoading = false;
                    setTimeout(() => {
                        IScroll1.refresh();
                    }, 600)
                    setTimeout(() => {
                        IScroll1.scrollTo(0, IScroll1.maxScrollY, 200)
                        IScroll1.enable();
                    }, 700)


                } else {
                    vm.handleError(res)
                }
            })
        },
        onZoom(url) {
            this.d_zoon = true
            this.d_z_url = url

        },
        onSend() {
            let _d = {};
            _d.message_group_id = this.dialog.info.message_group_id
            _d.type = 1;
            _d.content = this.dialog.info.content;
            this.send(_d)
            this.dialog.info.content = ''
        },
        //取对话数据
        send(data) {
            let url = '/admin/Kefu/add', vm = this;
            let _d = data;
         

            this.apiPost(url, _d).then((res) => {
                if (res.code) {
                    this.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //清空
        onReset() {
            this.formInline = {
                keyword: '',
            }
            this.get_list(1)
            this.isSearch = false;
        },
        //搜索
        onSearch(current_paged) {

            this.isSearch = true;
            current_paged = current_paged || 1;
            let searchData = this.formInline
            this.get_list(current_paged, searchData)
        },

        //取数据
        get_list(page, searchData) {
            page = page || 1;
            let url = '/admin/kefu/get_list?page=' + page,
                vm = this;
            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.list = res.data.list;
                    if (vm.isSearch == false) {
                        //通知全局商品分类数据
                        vm.setCatList(res.data.list)

                    }
                    vm.pages = res.data.pages
                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },



    },
    //组件初始化
    created() {
        this.get_list();
        this.setBreadcrumb(['前台', '在线客服'])
        setTimeout(() => {
            window.IScroll1 = new IScroll('#reply_list_content', { mouseWheel: true, scrollbars: true });
        }, 500)

    }, beforeRouteEnter(to, from, next) {
        next(vm => {
            if (window.IScroll1 == null || !window.IScroll1) {
                setTimeout(() => {
                    window.IScroll1 = new IScroll('#reply_list_content', { mouseWheel: true, scrollbars: true });
                }, 500)
            }

        })

    }
    , beforeRouteLeave(to, from, next) {
        if (window.IScroll1) {
            window.IScroll1.destroy();
        }

        window.IScroll1 = null;
        next()
    }

}
</script>
