webpackJsonp([7],{"3m5v":function(e,t,a){"use strict";var n=a(5);t.a={mixins:[n.default],data:function(){return{isSearch:!1,dalogi_loading:!1,formInline:{keyword:""},list:[]}},methods:{handle:function(e){var t=this,a={id:e.id};t.dalogi_loading=!0,this.apiPost("/admin/Disapply/handle",a).then(function(e){e.code?(t.$message.success(e.msg),t.isSearch?t.onSearch(t.pages.current_page):t.get_list()):t.handleError(e),t.dalogi_loading=!1})},onReset:function(){this.formInline={keyword:""},this.get_list(1),this.isSearch=!1},onSearch:function(e){this.isSearch=!0,e=e||1;var t=this.formInline;this.get_list(e,t)},get_list:function(e,t){e=e||1;var a="/admin/Disapply/get_list?page="+e,n=this;n.loading=!0,this.apiGet(a,t).then(function(e){e.code?(n.list=e.data.list,n.pages=e.data.pages):n.handleError(e),n.loading=!1})}},created:function(){this.get_list(),this.setBreadcrumb(["用户","分销申请"])}}},"b/vN":function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=a("3m5v"),l=a("rPyP"),i=a("VU/8"),r=i(n.a,l.a,!1,null,null,null);t.default=r.exports},rPyP:function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("div",{staticClass:"page_heade",on:{keyup:function(t){if(!("button"in t)&&e._k(t.keyCode,"enter",13,t.key))return null;e.onSearch()}}},[a("el-form",{attrs:{inline:!0,model:e.formInline}},[a("el-form-item",{attrs:{label:"用户ID/手机号码"}},[a("el-input",{staticStyle:{width:"180px"},attrs:{placeholder:"用户ID/手机号码"},model:{value:e.formInline.keyword,callback:function(t){e.$set(e.formInline,"keyword",t)},expression:"formInline.keyword"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:function(t){e.onSearch()}}},[e._v("搜索")]),e._v(" "),e.isSearch?a("el-button",{attrs:{type:"danger"},on:{click:e.onReset}},[e._v("清空搜索")]):e._e()],1)],1)],1),e._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading.body",value:e.loading,expression:"loading",modifiers:{body:!0}}],staticStyle:{width:"100%"},attrs:{data:e.list,border:""}},[a("el-table-column",{attrs:{prop:"user_id",label:"用户ID",width:"120"}}),e._v(" "),a("el-table-column",{attrs:{prop:"nickname",label:"申请人"}}),e._v(" "),a("el-table-column",{attrs:{prop:"phone_number",label:"申请人手机号码",width:"150"}}),e._v(" "),a("el-table-column",{attrs:{prop:"add_time",label:"申请时间",width:"180"}}),e._v(" "),a("el-table-column",{attrs:{prop:"status",label:"处理状态",width:"120"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(2==t.row.status?"已处理":"待处理")+"\n            ")]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"handle_time",label:"处理时间",width:"180"}}),e._v(" "),a("el-table-column",{attrs:{prop:"admin_user_id",label:"管理员ID",width:"100"}}),e._v(" "),a("el-table-column",{attrs:{prop:"admin_user_name",label:"管理员名称",width:"150"}}),e._v(" "),a("el-table-column",{attrs:{label:"操作",width:"150",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[1==t.row.status?a("el-button",{attrs:{type:"text",size:"small"},on:{click:function(a){e.handle(t.row)}}},[e._v("标记处理")]):e._e()]}}])})],1),e._v(" "),a("div",{staticClass:"pagination"},[parseInt(e.pages.total_page,10)>1?a("el-pagination",{attrs:{"current-page":parseInt(e.pages.current_page,10),"page-size":parseInt(e.pages.limit,10),total:e.pages.total,layout:"total, prev, pager, next,jumper"},on:{"current-change":e.handleCurrentChange}}):e._e()],1)],1)},l=[],i={render:n,staticRenderFns:l};t.a=i}});