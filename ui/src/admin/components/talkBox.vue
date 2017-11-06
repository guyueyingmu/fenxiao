<template>
  <div>
      <div class="reply_list_main" v-if="$store.state.talkBoxArray.length">
      
          <div class="reply-min-box" v-if="minshow" v-autoPosition>
              <i class="el-icon-information"></i> 与 <b>{{userInfo.nickname}}</b> 对话中 <a href="javascript:;" @click="onMinDisable">[恢复窗口]</a> </div> 
                <div class="reply_list_dialog" v-drop v-show="minshow ==false" :style="{'left':left}">
                      
                     
                    <div class="reply_list_box">
                      
                        <div class="sidebar">
                            <div>
                                <ul>
                                    <li :class="{'active':actived == item.user_id}" v-for="(item,k) in talkArray" :key="item.user_id" @click="selectUser(item.user_id)">
                                        <img :src="item.img_url" width="40" height="40">
                                         <div class="info">
                                             <div>{{item.nickname}}</div>
                                             <div class="des">{{lastMsg(k)}}</div>
                                         </div>
                                      <i class="el-icon-circle-close"  @click="close_reply(item.user_id)"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="reply_list_box_r">
                               <div  class="loadmore-msg" v-show="loadmore_f">{{loadmore_f_txt}}</div>
                            <div class="header" id="move_head">
                                    与 <b>{{userInfo.nickname}}</b> 对话中
                                    <div class="close_btn_right"><i class="el-icon-circle-close"  @click="close_reply"></i></div>
                                    <a  class="close_btn_right" @click="onMin" href="javascript:;" title="最小化"> <i class="el-icon-minus"></i></a>

                                   
                                    </div>
                            <div class="reply_list_content" :id="'reply_list_content_'+item.user_id"  :class="{'active':actived == item.user_id}" v-for="item in content_list" :key="item.user_id">
                                <div class="sd-scroller" v-loading="replyLoading">

                                    <div class="item" v-for="(p,idx) in item.list" :key="idx">
                                        <div class="item-box" :class="{'self':p.send_user == 2}">
                                            <div class="avt">
                                            <img :src="p.img_url" width="40" height="40">
                                        
                                            </div>
                                            <div class="item-content">
                                                <div class="time">{{p.add_time}} {{p.user_name}}</div>
                                                <span v-if="p.type == 1">{{p.content}}</span>

                                                <span v-if="p.type == 2" class="imgs" @click="onZoom(p.content.img_url)"><img :src="p.content.thumb_img_url" width="60" height="60"></span>

                                                <div class="pro_box" v-if="p.type == 3">
                                                    <div class="pro_box_flex">
                                                        <div class="pro_img"><img :src="p.content.good_img" width="40" height="40"></div>
                                                        <div class="info">
                                                            <div class="bl">{{p.content.good_title}}</div>
                                                            <div class="cost">￥{{p.content.good_price}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            <div class="sendBox"  @keyup.enter="onSend">
                                <div class="sendbox-tool">
                                    <el-upload class="upload-talkbox" action="/admin/Asset/upload?_ajax=1" name="image" :on-success="messageImgSuccess" :data="{img_type:`message_img`}" :show-file-list="false">
                                        <i class="el-icon-picture-outline"></i>
                                        </el-upload>
                                </div>
                                <div class="inputarea">
                                <textarea   placeholder="请输入内容" v-model="userInfo.content"></textarea>
                                </div>
                                <div class="sendbox-btn">
                                    <el-button size="small" @click="close_reply">关闭</el-button>
                                    <el-button   size="small" type="primary" @click="onSend()">发送</el-button>
                                </div>




                            </div>
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
import http from "@/assets/js/http";

export default {
  name: "talkBox",
  mixins: [http],
  props: ["talkArray"],
  data() {
    return {
      loadmore_f_txt: "加载更多信息...",
      reply_id: "reply_list_content",
      minshow: false,
      r_pages: {},
      d_z_url: "",
      current_user_id: 0,
      current_client_id: "",
      d_zoon: false,
      replyLoading: false,
      loadmore_f: false,
      reply_show: false,
      scrollEl: "",
      actived: "",
      content_list: [],

      left: "0px"
    };
  },
  computed: {
    userInfo() {
      let _d = null;

      for (let p of this.talkArray) {
        if (p.actived && p.actived != this.actived) {
          p.actived = false;
          this.actived = p.user_id;
          _d = p;
          break;
        } else {
          if (this.actived == p.user_id) {
            this.actived = p.user_id;
            _d = p;
          }
        }
      }
      this.get_talk_list(_d.message_group_id);
      return _d;
    }
  },

  methods: {
    lastMsg(idx) {
      if (this.content_list.length && this.content_list[idx]) {
        let item = this.content_list[idx].list[
          this.content_list[idx].list.length - 1
        ];
        if (item.type == 1) {
          return item.content;
        } else if (item.type == 2) {
          return "[图片信息]";
        } else if (item.type == 3) {
          return "[商品信息]";
        } else {
          return "";
        }
      } else {
        return "";
      }
    },
    selectUser(user_id) {
      this.actived = user_id;
      this.init();
    },
    onMin() {
      //最小化
      this.minshow = true;
    },
    onMinDisable() {
      //最大化
      this.minshow = false;
    },
    //数据初始化
    init() {
      this.replyLoading = true;
      let item = this.userInfo;
      item.content = "";

      this.bind_ws(item.user_id);
      this.current_user_id = item.user_id;
    },

    pushContent(data) {
      console.log(data);
      let vm = this;
      let _list = vm.content_list,
        _d = {};
      for (let _k = 0; _k < _list.length; _k++) {
        if (_list[_k].user_id == vm.actived) {
          _d = _list[_k].list;
          _d.push(data);
        //   console.log(_d);
          vm.scrollToEnd();
          break;
        }
      }
    },
    scrollToEnd() {
      let vm = this;
      let el = document.getElementById("reply_list_content_" + this.actived);
      if (el) {
        setTimeout(() => {
          el.scrollBy(0, el.scrollHeight);
        }, 200);
      }
    },
    new_msg(data) {
      let vm = this;

      if (data.send_user != 2) {
        //不是自己发的
        let msg = data.user_name + "：" + data.content;
        const h = this.$createElement;
        if (data.type == 2) {
          msg = "[图片]";
        } else if (data.type == 3) {
          msg = "[商品信息]";
        }

        let _c = this.$notify({
          title: "您有新消息",
          message: h("div", [
            msg,
            h("div", { style: "color: #888" }, data.add_time.substr(11))
          ]),
          type: "info",
          onClick: function() {
            let id = data.user_id;
            if (vm.minshow) {
              vm.minshow = false;
              _c.close();
            }
          }
        });
      }
    },
    close_reply(user_id) {
      let vm = this;
      //退出聊天分组
      let url = "/admin/Kefu/leave_group";
      this.apiPost(url, {
        client_id: this.current_client_id,
        user_id: user_id
      }).then(res => {
        vm.removeTalkBox(user_id);
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
        _d.message_group_id = this.userInfo.message_group_id;
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
          vm.content_output(res.data);
          //   if (res.data.pages.current_page < 2) {
          //   } else {
          // let _list = vm.content_list;
          // vm.content_list = res.data.list.concat(_list);
          // if (res.data.pages.current_page == res.data.pages.total_page) {
          //   vm.loadmore_f_txt = "已经没有更多信息了...";
          //   setTimeout(() => {
          //     vm.loadmore_f = false;
          //   }, 4000);
          // } else {
          //   vm.loadmore_f_txt = "加载更多信息了...";
          //   setTimeout(() => {
          //     vm.loadmore_f = false;
          //   }, 1000);
          // }
          //   }
          this.replyLoading = false;
        } else {
          vm.handleError(res);
        }
      });
    },
    content_output(data) {
      let vm = this;
      if (data.pages.current_page < 2) {
        let _d = {
          user_id: vm.actived,
          list: data.list,
          pages: data.pages
        };

        let _list = vm.content_list,
          isHas = false;
        for (let _k = 0; _k < _list.length; _k++) {
          if (_list[_k].user_id == vm.actived) {
            //  _list.splice(_k,1,_d);
            isHas = true;
            break;
          }
        }
        if (isHas == false) {
          vm.content_list.push(_d);
          vm.scrollToEnd();
        }
      }
    },
    onZoom(url) {
      this.d_zoon = true;
      this.d_z_url = url;
    },
    onSend() {
      let _d = {};
      _d.message_group_id = this.userInfo.message_group_id;
      _d.type = 1;
      _d.content = this.userInfo.content;
      this.send(_d);
      this.userInfo.content = "";
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
    //分页加载
    loadmore() {
      let vm = this;

      let message_group_id = this.userInfo.message_group_id;
      //   if (vm.IScroll.maxScrollY < 0 && vm.IScroll.y == 0) {
      //     let page = parseInt(this.r_pages.current_page, 10);
      //     if (page < parseInt(this.r_pages.total_page, 10)) {
      //       page = page + 1;
      //       this.loadmore_f = true;
      //       this.get_talk_list(message_group_id, page);
      //     }
      //   }
    },
    bind_ws() {
      let vm = this;
      const ip = "mall.minbbo.com";
      const ws = new WebSocket("ws://" + ip + ":8282");
      ws.onmessage = function(e) {
        // json数据转换成js对象
        var data = eval("(" + e.data + ")");
        var type = data.type || "";
        switch (type) {
          case "init":
            console.log("run bind_ws");
            let url = "/admin/Kefu/bind";
            vm.current_client_id = data.client_id;
            vm.apiPost(url, {
                client_id: data.client_id
              })
              .then(res => {
                console.log(res.msg);
              });
            break;
          case "msg":
            console.log("msg");
            vm.pushContent(data.content);
            break;
          default:
        }
      };
      
    }
  },

  created() {
    this.left = (window.innerWidth - 600) / 2 + "px";
   this.bind_ws();
  }
};
</script>
