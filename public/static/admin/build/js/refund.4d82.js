webpackJsonp([11],{"/ec/":function(e,t,a){"use strict";var l=a("RHyO"),o=a("zL8q");a.n(o);t.a={mixins:[l.default],components:{"el-date-picker":o.DatePicker},data:function(){return{dalogi_loading:!1,dialog_temp:{},dialogFormVisible:!1,dialogForm:{},tabs:"",value8:"",isSearch:!1,formInline:{keyword:"",user_phone:"",start_time:"",end_time:""},list:[]}},methods:{onRefund:function(e,t){this.dialogForm={handle_user:e.handle_user,handle_time:e.handle_time,handle_note:e.handle_note,status:e.status,id:e.id},this.dialogFormVisible=!0,this.dialog_temp=e},dialog_ok:function(){var e=this.dialogForm;this.refundHandle(e)},fromDate:function(e){if(e){var t=e.split(" - ");this.formInline.start_time=t[0],this.formInline.end_time=t[1]}},fromDate3:function(e){this.dialogForm.handle_time=e},onSelectedTabs:function(e){var t=0==e.name?"":e.name,a={status:t};this.get_list(1,a)},onReset:function(){this.formInline={keyword:"",user_phone:"",status:"",start_time:"",end_time:""},this.get_list(1),this.isSearch=!1},onSearch:function(e){this.isSearch=!0,e=e||1;var t=this.formInline;this.get_list(e,t)},get_list:function(e,t){e=e||1;var a="/admin/refund/get_list?page="+e,l=this;l.loading=!0,this.apiGet(a,t).then(function(e){e.code?(l.list=e.data.list,l.pages=e.data.pages):l.handleError(e),l.loading=!1})},refundHandle:function(e,t){var a=this;a.loading=!0,this.apiPost("/admin/refund/handle",e).then(function(e){e.code?(a.dialogFormVisible=!1,a.$message({type:"success",message:e.msg}),a.get_list()):a.handleError(e),a.loading=!1})}},created:function(){this.get_list(),this.setBreadcrumb(["订单","退款申请"])}}},"1w3X":function(e,t,a){"use strict";var l=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("div",{staticClass:"tabs_p"},[a("el-tabs",{attrs:{type:"card"},on:{"tab-click":e.onSelectedTabs},model:{value:e.tabs,callback:function(t){e.tabs=t},expression:"tabs"}},[a("el-tab-pane",{attrs:{label:"全部",name:"0"}}),e._v(" "),a("el-tab-pane",{attrs:{label:"退款申请",name:"1"}}),e._v(" "),a("el-tab-pane",{attrs:{label:"已退款",name:"2"}}),e._v(" "),a("el-tab-pane",{attrs:{label:"已拒绝",name:"3"}})],1)],1),e._v(" "),a("div",{staticClass:"page_heade"},[a("el-form",{attrs:{inline:!0,model:e.formInline}},[a("el-form-item",{attrs:{"label-width":"1"}},[a("el-input",{staticStyle:{width:"200px"},attrs:{placeholder:"订单编号"},model:{value:e.formInline.keyword,callback:function(t){e.$set(e.formInline,"keyword",t)},expression:"formInline.keyword"}})],1),e._v(" "),a("el-form-item",{attrs:{"label-width":"1"}},[a("el-input",{staticStyle:{width:"200px"},attrs:{placeholder:"用户手机"},model:{value:e.formInline.user_phone,callback:function(t){e.$set(e.formInline,"user_phone",t)},expression:"formInline.user_phone"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"申请时间"}},[a("el-date-picker",{attrs:{type:"daterange",align:"right",placeholder:"选择日期范围","picker-options":e.pickerOptions},on:{change:e.fromDate},model:{value:e.value7,callback:function(t){e.value7=t},expression:"value7"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:function(t){e.onSearch()}}},[e._v("搜索")]),e._v(" "),e.isSearch?a("el-button",{attrs:{type:"danger"},on:{click:e.onReset}},[e._v("清空搜索")]):e._e()],1)],1)],1),e._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:e.list,border:""}},[a("el-table-column",{attrs:{prop:"order_number",label:"订单编号",width:"120"}}),e._v(" "),a("el-table-column",{attrs:{prop:"total_amount",label:"退款金额",width:"120"},scopedSlots:e._u([{key:"default",fn:function(t){return[a("span",[e._v("￥"+e._s(t.row.total_amount))])]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"order_status_txt",label:"订单状态",width:"120"}}),e._v(" "),a("el-table-column",{attrs:{prop:"pay_status_txt",label:"支付状态",width:"120"}}),e._v(" "),a("el-table-column",{attrs:{prop:"user_id",label:"下单用户ID",width:"120"}}),e._v(" "),a("el-table-column",{attrs:{prop:"phone_number",label:"用户手机"}}),e._v(" "),a("el-table-column",{attrs:{prop:"add_time",label:"申请时间"}}),e._v(" "),a("el-table-column",{attrs:{prop:"handle_user",label:"处理员",width:"100"}}),e._v(" "),a("el-table-column",{attrs:{prop:"status",label:"处理状态",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[1==t.row.status?a("span",[e._v("未处理")]):2==t.row.status?a("span",[e._v("同意")]):a("span",[e._v("拒绝")])]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"handle_time",label:"处理时间"}}),e._v(" "),a("el-table-column",{attrs:{prop:"handle_note",label:"处理备注"}}),e._v(" "),a("el-table-column",{attrs:{label:"操作",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[1==t.row.status||5==t.row.pay_status?a("el-button",{attrs:{type:"text",size:"small"},on:{click:function(a){e.onRefund(t.row,t.row.$index)}}},[e._v("立即退款")]):e._e()]}}])})],1),e._v(" "),a("div",{staticClass:"pagination"},[parseInt(e.pages.total_page,10)>1?a("el-pagination",{attrs:{"current-page":parseInt(e.pages.current_page,10),"page-size":parseInt(e.pages.limit,10),total:e.pages.total,layout:"total, prev, pager, next,jumper"},on:{"current-change":e.handleCurrentChange}}):e._e()],1),e._v(" "),a("el-dialog",{directives:[{name:"loading",rawName:"v-loading",value:e.dalogi_loading,expression:"dalogi_loading"}],attrs:{title:"退款",visible:e.dialogFormVisible,"close-on-click-modal":!1,width:"700px"},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[a("el-form",{attrs:{model:e.dialogForm,inline:!0}},[a("el-form-item",{attrs:{label:"处理员","label-width":"100px"}},[a("el-input",{attrs:{"auto-complete":"off",placeholder:"发货员"},model:{value:e.dialogForm.handle_user,callback:function(t){e.$set(e.dialogForm,"handle_user",t)},expression:"dialogForm.handle_user"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"处理时间","label-width":"100px"}},[a("el-date-picker",{staticStyle:{width:"200px"},attrs:{type:"datetime",editable:!1,placeholder:"选择日期"},on:{change:e.fromDate3},model:{value:e.dialogForm.handle_time,callback:function(t){e.$set(e.dialogForm,"handle_time",t)},expression:"dialogForm.handle_time"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"处理状态","label-width":"100px"}},[a("el-select",{staticStyle:{width:"193px"},attrs:{placeholder:"处理状态"},model:{value:e.dialogForm.status,callback:function(t){e.$set(e.dialogForm,"status",t)},expression:"dialogForm.status"}},[a("el-option",{attrs:{label:"未处理",value:1}}),e._v(" "),a("el-option",{attrs:{label:"同意",value:2}}),e._v(" "),a("el-option",{attrs:{label:"拒绝",value:3}})],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"备注","label-width":"100px"}},[a("el-input",{attrs:{"auto-complete":"off",placeholder:"备注"},model:{value:e.dialogForm.handle_note,callback:function(t){e.$set(e.dialogForm,"handle_note",t)},expression:"dialogForm.handle_note"}})],1)],1),e._v(" "),a("div",{staticClass:"dialog_main"},[a("h4",{staticClass:"dialog_order_title"},[e._v("订单信息")]),e._v(" "),a("div",{staticClass:"dialog_order_info"},[a("el-row",[a("el-col",{attrs:{span:12}},[e._v("\n                        订单编号：\n                        "),a("span",[e._v(e._s(e.dialog_temp.order_number))]),e._v(" "),a("br"),e._v(" 下单时间：\n                        "),a("span",[e._v(e._s(e.dialog_temp.order_add_time))])]),e._v(" "),a("el-col",{attrs:{span:12}},[e._v("\n                        下单用户：\n                        "),a("span",[e._v(e._s(e.dialog_temp.nickname))]),e._v(" "),a("br"),e._v(" 用户手机：\n                        "),a("span",[e._v(e._s(e.dialog_temp.phone_number))])])],1)],1)]),e._v(" "),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("取 消")]),e._v(" "),a("el-button",{attrs:{type:"primary"},on:{click:e.dialog_ok}},[e._v("确 定")])],1)],1)],1)},o=[],n={render:l,staticRenderFns:o};t.a=n},TN60:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var l=a("/ec/"),o=a("1w3X"),n=a("VU/8"),i=n(l.a,o.a,!1,null,null,null);t.default=i.exports}});