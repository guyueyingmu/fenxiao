webpackJsonp([23],{"+m9x":function(t,i,e){"use strict";var a=e(2);i.a={mixins:[a.default],data:function(){return{applied:0}},methods:{check_apply:function(){var t=this,i={};this.apiGet("/mini/Home/dis_applly_check",i).then(function(i){i.code?i.data>0&&(t.applied=1):t.handleError(i)})},save_data:function(){var t=this,i={};this.apiPost("/mini/Home/dis_apply",i).then(function(i){i.code?t.$msg(i.msg):t.handleError(i)})}},created:function(){this.setTitle("我要代理"),this.check_apply()}}},"9jjH":function(t,i,e){"use strict";var a=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{staticStyle:{width:"100%"},attrs:{src:"/static/mini/img/code.png"}}),t._v(" "),e("div",{staticStyle:{padding:"13px"}},[0==t.applied?e("a",{staticClass:"ui-btn ui-btn-block",attrs:{herf:"javascript:;"},on:{click:function(i){t.save_data()}}},[t._v("我要代理")]):t._e()])])},n=[],s={render:a,staticRenderFns:n};i.a=s},B5E8:function(t,i,e){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var a=e("+m9x"),n=e("9jjH"),s=e("VU/8"),c=s(a.a,n.a,!1,null,null,null);i.default=c.exports}});
//# sourceMappingURL=daili.14e7.js.map