webpackJsonp([21],{QYbP:function(e,t,a){"use strict";var l=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("div",{staticClass:"tabs_p"},[a("el-tabs",{attrs:{type:"card"},on:{"tab-click":e.onSelectedTabs},model:{value:e.tabs,callback:function(t){e.tabs=t},expression:"tabs"}},[a("el-tab-pane",{attrs:{label:"分销总额奖",name:"1"}}),e._v(" "),a("el-tab-pane",{attrs:{label:"已获佣",name:"2"}})],1)],1),e._v(" "),a("div",{staticClass:"page_heade"},[a("el-form",{attrs:{inline:!0,model:e.formInline}},[a("el-form-item",{attrs:{label:"分销商用户ID"}},[a("el-input",{staticStyle:{width:"140px"},attrs:{placeholder:"分销商用户ID"},model:{value:e.formInline.keyword,callback:function(t){e.$set(e.formInline,"keyword",t)},expression:"formInline.keyword"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"分销商手机"}},[a("el-input",{staticStyle:{width:"140px"},attrs:{placeholder:"分销商手机"},model:{value:e.formInline.user_phone,callback:function(t){e.$set(e.formInline,"user_phone",t)},expression:"formInline.user_phone"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:function(t){e.onSearch()}}},[e._v("搜索")]),e._v(" "),e.isSearch?a("el-button",{attrs:{type:"danger"},on:{click:e.onReset}},[e._v("清空搜索")]):e._e()],1)],1)],1),e._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading.body",value:e.loading,expression:"loading",modifiers:{body:!0}}],staticStyle:{width:"100%"},attrs:{data:e.list,border:""}},[a("el-table-column",{attrs:{prop:"earn_user_id",label:"用户ID"}}),e._v(" "),a("el-table-column",{attrs:{prop:"nickname",label:"用户呢称"}}),e._v(" "),a("el-table-column",{attrs:{prop:"phone_number",label:"手机号码",width:"110"}}),e._v(" "),a("el-table-column",{attrs:{prop:"earn_time",label:"团队奖获得日期"}}),e._v(" "),a("el-table-column",{attrs:{prop:"earn_amount",label:"获得金额"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(2==t.row.status?t.row.earn_amount_input:t.row.earn_amount)+"\n            ")]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"member_num",label:"发展会员数",width:"150"}}),e._v(" "),a("el-table-column",{attrs:{prop:"level",label:"获奖级别"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(1==t.row.level?"一级获佣":"二级获佣")+"\n            ")]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"status",label:"获佣状态"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(1==t.row.status?"等待获佣":"已获佣")+"\n            ")]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"add_time",label:"添加时间",width:"170"}}),e._v(" "),a("el-table-column",{attrs:{prop:"admin_user_id",label:"管理员ID"}}),e._v(" "),a("el-table-column",{attrs:{prop:"admin_user_name",align:"center",label:"管理员名称"}}),e._v(" "),a("el-table-column",{attrs:{label:"操作",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[1==t.row.status?a("el-button",{attrs:{type:"text",size:"small"},on:{click:function(a){e.open_win(t.row)}}},[e._v("确定获佣")]):e._e()]}}])})],1),e._v(" "),a("div",{staticClass:"pagination"},[parseInt(e.pages.total_page,10)>1?a("el-pagination",{attrs:{"current-page":parseInt(e.pages.current_page,10),"page-size":parseInt(e.pages.limit,10),total:e.pages.total,layout:"total, prev, pager, next,jumper"},on:{"current-change":e.handleCurrentChange}}):e._e()],1),e._v(" "),a("div",{staticStyle:{color:"#8492A6"}},[e._v("\n        备注： 每个月由系统统计当月分销商是否完成团队奖指标（分销设置中设置），完成则计算奖励。\n    ")]),e._v(" "),a("el-dialog",{directives:[{name:"loading",rawName:"v-loading",value:e.dalogi_loading,expression:"dalogi_loading"}],attrs:{title:"提示",visible:e.dialogFormVisible,"close-on-click-modal":!1},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[a("el-form",{attrs:{model:e.dialogForm,inline:!0}},[a("el-form-item",{attrs:{label:"获佣金额","label-width":"100px"}},[a("el-input",{attrs:{"auto-complete":"off",placeholder:"获佣金额"},model:{value:e.dialogForm.earn_amount,callback:function(t){e.$set(e.dialogForm,"earn_amount",t)},expression:"dialogForm.earn_amount"}})],1)],1),e._v(" "),a("div",{staticStyle:{color:"#8492A6","padding-top":"15px"}},[e._v("确定获佣后，则添加金额进该获佣分销商的获佣总额和账户余额！")]),e._v(" "),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{type:"primary"},on:{click:function(t){e.post_handle()}}},[e._v("确 定")])],1)],1)],1)},n=[],o={render:l,staticRenderFns:n};t.a=o},QpKk:function(e,t,a){"use strict";var l=a("RHyO");t.a={mixins:[l.default],data:function(){return{tabs:"1",isSearch:!1,dialogFormVisible:!1,dalogi_loading:!1,formInline:{keyword:"",user_phone:"",status:1},list:[],dialogForm:{id:"",earn_amount:""}}},methods:{open_win:function(e){this.dialogForm.id=e.id,this.dialogForm.earn_amount=e.earn_amount,this.dialogFormVisible=!0},post_handle:function(){var e=this,t=this.dialogForm;e.dalogi_loading=!0,this.apiPost("/admin/Teamprize/handle",t).then(function(t){t.code?(e.$message.success(t.msg),e.dialogFormVisible=!1,e.isSearch?e.onSearch(e.pages.current_page):e.get_list()):e.handleError(t),e.dalogi_loading=!1})},onSelectedTabs:function(e){var t=e.name,a={status:t};this.formInline.status=t,this.get_list(1,a)},onReset:function(){this.formInline.keyword="",this.formInline.user_phone="",this.get_list(1,this.formInline),this.isSearch=!1},onSearch:function(e){this.isSearch=!0,e=e||1;var t=this.formInline;this.get_list(e,t)},get_list:function(e,t){e=e||1;var a="/admin/Teamprize/get_list?page="+e,l=this;l.loading=!0,this.apiGet(a,t).then(function(e){e.code?(l.list=e.data.list,l.pages=e.data.pages):l.handleError(e),l.loading=!1})}},created:function(){this.get_list(1,{status:1}),this.setBreadcrumb(["分销","分销团队奖"])}}},SoRw:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var l=a("QpKk"),n=a("QYbP"),o=a("VU/8"),i=o(l.a,n.a,!1,null,null,null);t.default=i.exports}});