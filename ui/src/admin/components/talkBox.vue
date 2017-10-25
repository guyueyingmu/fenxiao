<template>
  <div>
      <div class="reply_list_main">
          <div class="reply-min-box" v-if="minshow"><i class="el-icon-information"></i> 与 <b>{{dialog.info.nickname}}</b> 对话中 <a href="javascript:;" @click="onMinDisable">[恢复窗口]</a> </div>
        
    
                <div class="reply_list_dialog" v-drop v-show="minshow ==false">
                       <div  class="loadmore-msg" v-show="loadmore_f">加载更多信息...</div>
                     
                    <div class="reply_list_box">
                        <div class="header" id="move_head"><a  class="reply-min-btn" @click="onMin" href="javascript:;" title="最小化"><i class="el-icon-minus"></i></a>与 <b>{{dialog.info.nickname}}</b> 对话中<div title="关闭" class="close_btn_right" @click="close_reply"><i class="el-icon-circle-cross"></i></div></div>
                        <div class="reply_list_content" id="reply_list_content">
                            <div class="sd-scroller" v-loading="replyLoading">

                                <div class="item" v-for="(item,idx) in dialog.list" :key="idx">
                                    <div class="item-box" :class="{'self':item.send_user == 2}">
                                        <div class="avt">
                                        <img :src="item.img_url" width="40" height="40">
                                            <span class="name">{{item.user_name}}</span>
                                        </div>
                                        <div class="item-content">
                                            <span v-if="item.type == 1">{{item.content}}</span>

                                            <span v-if="item.type == 2" @click="onZoom(item.content.img_url)"><img :src="item.content.thumb_img_url" width="60" height="60"></span>

                                            <div class="pro_box" v-if="item.type == 3">
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
                                    <el-upload class="upload-demo" action="/admin/Asset/upload?_ajax=1" name="image" :on-success="messageImgSuccess" :data="{img_type:`message_img`}" :show-file-list="false">
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
    

        </div>
        <el-dialog v-model="d_zoon" size="tiny">
            <img width="100%" :src="d_z_url" alt="">
        </el-dialog>
  </div>
</template>
<script>
import Iscroll from "@/assets/js/iscroll";
import http from "@/assets/js/http";

export default {
  name: "talkBox",
  mixins: [http],
  data() {
    return {
        minshow:false,
      r_pages: {},
      d_z_url: "",
      current_user_id: 0,
      current_client_id: "",
      d_zoon: false,
      replyLoading: false,
      loadmore_f: false,
      reply_show: false,
      dialog: {
        info: {
          content: "",
          user_id: ""
        },
        list: []
      }
    };
  },
  methods: {
      onMin(){
          //最小化
          this.minshow = true;
      },
         onMinDisable(){
          //最大化
          this.minshow = false;
      },
    //回复
    open_replyDialog(item) {
      this.dialog.list = [];
      this.dialog.info = JSON.parse(JSON.stringify(item));
      this.dialog.info.content = "";
      this.reply_show = true;
      this.replyLoading = true;
      //   document.body.style.overflow = "hidden";
      this.bind_ws(item.user_id);
      this.current_user_id = item.user_id;
      this.get_talk_list(item.message_group_id);
    },
    bind_ws(user_id) {
      // const ip = window.location.hostname;
      const ip = "mall.minbbo.com";
      const ws = new WebSocket("ws://" + ip + ":8282");
      let vm = this;
      ws.onmessage = function(e) {
        // json数据转换成js对象
        var data = eval("(" + e.data + ")");
        var type = data.type || "";
        switch (type) {
          case "init":
            let url = "/admin/Kefu/bind";
            vm.current_client_id = data.client_id;
            vm
              .apiPost(url, { client_id: data.client_id, user_id: user_id })
              .then(res => {
                console.log(res);
                if (res.code) {
                  // vm.serverInit = false
                }
              });
            break;
          case "msg":
            console.log(1, data.content);
            vm.dialog.list.push(data.content);
            setTimeout(() => {
              IScroll1.refresh();
            }, 200);
            setTimeout(() => {
              IScroll1.scrollTo(0, IScroll1.maxScrollY, 200);
            }, 300);
            break;
          // 当mvc框架调用GatewayClient发消息时直接alert出来
          default:
          //                alert(e.data);
        }
      };
    },
    close_reply() {
      this.reply_show = false;
      IScroll1 = null;

      let user_id = this.current_user_id;
      this.current_user_id = 0;
      let vm = this;
      //退出聊天分组
      let url = "/admin/Kefu/leave_group";
      this.apiPost(url, {
        client_id: this.current_client_id,
        user_id: user_id
      }).then(res => {
        vm.$store.state.talkBox_show = false;
        vm.$message({
          message: "成功退出对话",
          type: "success"
        });
      });
    },
    //小图上传成功
    messageImgSuccess(res, file) {
      if (res.code) {
        let _d = {};
        _d.message_group_id = this.dialog.info.message_group_id;
        _d.type = 2;
        _d.content = res.data.img_path;
        this.send(_d);
      }
    },
    //小图上传前处理
    beforeAvatarUpload(file) {
      const isJPG = file.type == "image/jpeg";
      const isPNG = file.type == "image/png";
      let isTypeOk = false;
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isLt2M) {
        this.$message.error("上传图片大小不能超过 2MB!");
      }

      if (!isPNG && !isJPG) {
        this.$message.error("上传图片只能是 PNG 或 JPG 格式!");
      } else {
        isTypeOk = true;
      }
      return isLt2M && isTypeOk;
    },
    //取对话数据
    get_talk_list(message_group_id, page) {
      page = page || 1;
      let url =
          "/admin/Kefu/detail?message_group_id=" +
          message_group_id +
          "&page=" +
          page,
        vm = this;
      this.apiGet(url).then(res => {
        if (res.code) {
          vm.r_pages = res.data.pages;

          if (res.data.pages.current_page < 2) {
            vm.dialog.list = res.data.list;
            setTimeout(() => {
              IScroll1.refresh();
            }, 600);
            setTimeout(() => {
              IScroll1.scrollTo(0, IScroll1.maxScrollY, 200);
              IScroll1.enable();
            }, 750);
          } else {
            let _list = vm.dialog.list;
            vm.dialog.list = res.data.list.concat(_list);
            vm.loadmore_f = false;
          }
          this.replyLoading = false;
        } else {
          vm.handleError(res);
        }
      });
    },
    onZoom(url) {
      this.d_zoon = true;
      this.d_z_url = url;
    },
    onSend() {
      let _d = {};
      _d.message_group_id = this.dialog.info.message_group_id;
      _d.type = 1;
      _d.content = this.dialog.info.content;
      this.send(_d);
      this.dialog.info.content = "";
    },
    //取对话数据
    send(data) {
      let url = "/admin/Kefu/add",
        vm = this;
      let _d = data;

      this.apiPost(url, _d).then(res => {
        if (res.code) {
          // vm.$msg(res.msg);
        } else {
          vm.handleError(res);
        }
      });
    },
    loadmore() {
      console.log("load more");
      let message_group_id = this.dialog.info.message_group_id;
      if (window.IScroll1.maxScrollY < 0 && window.IScroll1.y == 0) {
        let page = parseInt(this.r_pages.current_page, 10);
        if (page < parseInt(this.r_pages.total_page, 10)) {
          page = page + 1;
          this.loadmore_f = true;
          this.get_talk_list(message_group_id, page);
        }
      }
    }
  },
  created() {
    let vm = this;
    this.open_replyDialog(this.$store.state.talkBoxInfo);

    setTimeout(() => {
      if (window.IScroll1 == null || !window.IScroll1) {
        window.IScroll1 = new IScroll("#reply_list_content", {
          mouseWheel: true,
          scrollbars: true
        });

        window.IScroll1.on("scrollEnd", function() {
          vm.loadmore();
        });
      }
    }, 500);
  }
};
</script>
