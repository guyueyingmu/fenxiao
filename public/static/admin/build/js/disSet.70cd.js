webpackJsonp([12],{"34WU":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=i("TJ2k"),a=i("aIWt"),r=i("VU/8"),s=r(n.a,a.a,null,null,null);e.default=s.exports},TJ2k:function(t,e,i){"use strict";var n=i("mvHQ"),a=i.n(n),r=i("//TE");e.a={mixins:[r.a],data:function(){return{ruleForm:[],c_type:""}},methods:{submitForm:function(){this.save_set(this.ruleForm)},save_set:function(t){var e={data:a()(t)},i=this;i.loading=!0,this.apiPost("/admin/setting/set",e).then(function(t){t.code?(console.log(t),i.$message({type:"success",message:t.msg})):i.handleError(t),i.loading=!1})},get_set:function(t,e){t=t||"";var i="/admin/setting/get_set?c_type="+t,n=this;n.loading=!0,this.apiGet(i,e).then(function(t){t.code?n.ruleForm=t.data:n.handleError(t),n.loading=!1})},initData:function(){this.c_type=this.$route.params.c_type;var t=["分销","分销设置"];2==this.c_type&&(t=["用户","积分设置"]),this.get_set(this.c_type),this.setBreadcrumb(t)}},watch:{$route:"initData"},created:function(){this.initData()}}},aIWt:function(t,e,i){"use strict";var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticStyle:{padding:"5% 10% 0 0"}},[i("el-form",{ref:"ruleForm",staticClass:"demo-ruleForm",attrs:{"label-width":"230px"}},[t._l(t.ruleForm,function(e,n){return i("el-form-item",{key:e.id,attrs:{label:e.show_name,prop:e.c_name}},[i("el-input",{attrs:{disabled:0==n&&1==t.c_type},model:{value:e.c_value,callback:function(t){e.c_value=t},expression:"item.c_value"}})],1)}),t._v(" "),i("el-form-item",[i("el-button",{attrs:{type:"primary"},on:{click:function(e){t.submitForm("ruleForm")}}},[t._v("保存设置")])],1)],2)],1)},a=[],r={render:n,staticRenderFns:a};e.a=r},mvHQ:function(t,e,i){t.exports={default:i("qkKv"),__esModule:!0}},qkKv:function(t,e,i){var n=i("FeBl"),a=n.JSON||(n.JSON={stringify:JSON.stringify});t.exports=function(t){return a.stringify.apply(a,arguments)}}});