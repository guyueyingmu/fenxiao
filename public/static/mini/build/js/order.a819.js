webpackJsonp([17],{J8cS:function(t,i,e){"use strict";var s=e("MSrN");i.a={name:"favorite",mixins:[s.default],data:function(){return{list:[],page_info:{},search:{keyword:0}}},methods:{exchange:function(t){var i=this,e={order_id:this.list[t].id};this.$confirm({msg:"确定申请换货？<br />申请换货后将有客服人员跟进处理！",yes:function(){i.apiPost("/mini/Exchange/add",e).then(function(e){e.code?(i.list[t].exchange=0,i.list[t].refund=0,i.$msg(e.msg)):i.handleError(e)})}})},refund:function(t){var i=this,e={order_id:this.list[t].id};this.$confirm({msg:"确定申请退款？<br />申请换货后将有客服人员跟进处理！",yes:function(){i.apiPost("/mini/Refund/add",e).then(function(e){e.code?(i.list[t].refund=0,i.list[t].exchange=0,i.$msg(e.msg)):i.handleError(e)})}})},loadMore:function(){var t=this,i=parseInt(this.page_info.current_page,10);clearTimeout(this.timer),this.timer=setTimeout(function(){i<t.page_info.total_page&&t.get_list(i+1)},500)},onSearch:function(t){this.search.keyword=t;var i={status:this.search.keyword};this.get_list(1,i)},get_list:function(t,i){t=t||1;var e="/mini/Order/get_list?page="+t,s=this;this.apiGet(e,i).then(function(i){if(i.code)if(s.page_info=i.data.pages,t<2)s.list=i.data.list;else{var e=s.list;e=e.concat(i.data.list),s.list=e}else s.handleError(i)})}},created:function(){this.setTitle("我的订单"),this.get_list()}}},V64i:function(t,i,e){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var s=e("J8cS"),n=e("iIBT"),a=e("VU/8"),o=a(s.a,n.a,!1,null,null,null);i.default=o.exports},iIBT:function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}]},[e("div",{staticClass:"ui-tags"},[e("span",{class:{active:0==t.search.keyword},on:{click:function(i){t.onSearch(0)}}},[t._v("全部")]),t._v(" "),e("span",{class:{active:1==t.search.keyword},on:{click:function(i){t.onSearch(1)}}},[t._v("待付款")]),t._v(" "),e("span",{class:{active:2==t.search.keyword},on:{click:function(i){t.onSearch(2)}}},[t._v("待收货")]),t._v(" "),e("span",{class:{active:5==t.search.keyword},on:{click:function(i){t.onSearch(5)}}},[t._v("已完成")]),t._v(" "),e("span",{class:{active:4==t.search.keyword},on:{click:function(i){t.onSearch(4)}}},[t._v("已取消")])]),t._v(" "),t.list.length>0?e("ul",{directives:[{name:"infinite-scroll",rawName:"v-infinite-scroll",value:t.loadMore,expression:"loadMore"}],staticClass:"order-list",attrs:{"infinite-scroll-disabled":"loadComment","infinite-scroll-immediate-check":!1,"infinite-scroll-distance":"10"}},t._l(t.list,function(i,s){return e("li",{key:s},[e("div",{staticClass:"h",on:{click:function(e){t.goto("/order_detail/order_id/"+i.id)}}},[t._v("订单号："+t._s(i.order_number)+"\n                "),e("span",{staticClass:"status"},[t._v(t._s(i.order_status_txt))])]),t._v(" "),t._l(i.orders_goods,function(s,n){return e("div",{key:n,staticClass:"m",on:{click:function(e){t.goto("/order_detail/order_id/"+i.id)}}},[e("img",{directives:[{name:"lazy",rawName:"v-lazy",value:s.good_img,expression:"good.good_img"}],attrs:{width:"50",height:"50"}}),t._v(" "),e("div",{staticClass:"info"},[e("div",{staticClass:"title"},[t._v(t._s(s.good_title))])]),t._v(" "),e("div",{staticClass:"tool"},[3==i.pay_method?e("div",[t._v("积分 "+t._s(s.credits))]):e("div",[t._v("￥"+t._s(s.price))]),t._v(" "),e("div",[t._v("x "+t._s(s.buy_num))])])])}),t._v(" "),e("div",{staticClass:"b"},[3==i.pay_method?e("div",[t._v("订单总额：积分 "+t._s(i.minus_credits))]):e("div",[t._v("订单总额：￥"+t._s(i.total_amount))]),t._v(" "),e("div",[1==i.pay_status&&2!=i.pay_method?e("button",{staticClass:"ui-btn active",attrs:{type:"button"},on:{click:function(e){t.goto("/pay/order_id/"+i.id)}}},[t._v("付款")]):t._e(),t._v(" "),1==i.comment_status?e("button",{staticClass:"ui-btn active",attrs:{type:"button"},on:{click:function(e){t.goto("/start/order_id/"+i.id)}}},[t._v("评价")]):t._e(),t._v(" "),1==i.refund?e("button",{staticClass:"ui-btn active",attrs:{type:"button"},on:{click:function(i){t.refund(s)}}},[t._v("退款")]):t._e(),t._v(" "),1==i.exchange?e("button",{staticClass:"ui-btn active",attrs:{type:"button"},on:{click:function(i){t.exchange(s)}}},[t._v("换货")]):t._e()])])],2)})):e("div",{staticClass:"nodata"},[e("i",{staticClass:"iconfont icon-tongyongmeiyoushuju"}),t._v(" "),e("div",[t._v("您还没有订单记录~")])])])},n=[],a={render:s,staticRenderFns:n};i.a=a}});
//# sourceMappingURL=order.a819.js.map