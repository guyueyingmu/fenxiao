webpackJsonp([24],{"55fC":function(s,t,i){"use strict";var a=i("mvHQ"),o=i.n(a),d=i(2);t.a={name:"class",mixins:[d.default],data:function(){return{post_good_list:[],info:{good_list:[],address:{},show_address:1,total_amount:"",total_credits:1},address:{}}},methods:{get_good_info:function(){var s=this;this.apiGet("/mini/Order/check_order",this.post_good_list).then(function(t){if(t.code){if(s.info=t.data,1==t.data.show_address){var i=window.localStorage.getItem("__Select_Address__");s.address=i?JSON.parse(i):{}}}else s.handleError(t)})},save_data:function(){if(this.info.show_address&&!this.address.id)return console.log(this.address.id),this.$msg("请选择配送地址"),!1;var s=this,t={good_info:o()(s.post_good_list),address_id:s.address.id};this.apiPost("/mini/Order/add_order",t).then(function(t){t.code?(s.$msg(t.msg),setTimeout(function(){2==t.data.pay_method?s.goto("/success/order_id/"+t.data.order_id):s.goto("/pay/order_id/"+t.data.order_id)},500),window.localStorage.removeItem("__CART__")):s.handleError(t)})}},created:function(){this.setTitle("确认订单");var s=this.$store.state.cart;s.length<1&&(s=window.localStorage.getItem("__CART__"))&&(s=JSON.parse(s)),this.post_good_list=s,this.get_good_info()}}},6100:function(s,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=i("55fC"),o=i("ZND7"),d=i("VU/8"),e=d(a.a,o.a,!1,null,null,null);t.default=e.exports},ZND7:function(s,t,i){"use strict";var a=function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",{staticClass:"order"},[1==s.info.show_address?i("ul",{staticClass:"ui-links"},[s.address.id?i("li",{on:{click:function(t){s.goto("/address/is_use/1")}}},[i("div",{staticClass:"title add_info"},[i("div",{staticClass:"flex"},[i("span",[s._v("收货人：")]),s._v(" "),i("i",[s._v(s._s(s.address.user_name))])]),s._v(" "),i("div",{staticClass:"flex"},[i("span",[s._v("手机号：")]),s._v(" "),i("i",[s._v(s._s(s.address.phone))])]),s._v(" "),i("div",{staticClass:"flex"},[i("span",[s._v("配送至：")]),s._v(" "),s.address.province==s.address.city?i("i",[s._v("\n                        "+s._s(s.address.city)+"市 "+s._s(s.address.area)+" "+s._s(s.address.address)+"\n                    ")]):i("i",[s._v(s._s(s.address.province)+"省 "+s._s(s.address.city)+"市 "+s._s(s.address.area)+" "+s._s(s.address.address))])])]),s._v(" "),i("i",{staticClass:"iconfont icon-arrow"})]):i("li",{staticClass:"noAdd",on:{click:function(t){s.goto("/address/is_use/1")}}},[s._m(0)])]):s._e(),s._v(" "),1==s.info.show_address?i("div",{staticStyle:{height:"10px"}}):s._e(),s._v(" "),s.info.good_list.length>0?i("div",[i("ul",{staticClass:"thumb-list l-t"},s._l(s.info.good_list,function(t,a){return i("li",{key:t.id},[i("img",{attrs:{src:t.good_img,width:"70",height:"70"}}),s._v(" "),i("div",{staticClass:"info"},[i("div",{staticClass:"title"},[s._v(s._s(t.good_title))]),s._v(" "),i("div",{staticClass:"tool"},[i("span",{staticClass:"grey"},[s._v("x "+s._s(t.good_count))]),s._v(" "),4==t.good_type||5==t.good_type?i("span",{staticClass:"red"},[s._v("积分\n                            "),i("em",[s._v(s._s(t.credits))])]):i("span",{staticClass:"red"},[s._v("￥\n                            "),i("em",[s._v(s._s(t.price))])])])])])})),s._v(" "),i("ul",{staticClass:"ui-fixd"},[i("li",[i("div",{staticClass:"m"},[s._v("商品总计")]),s._v(" "),4==s.info.good_list[0].good_type||5==s.info.good_list[0].good_type?i("div",{staticClass:"r"},[s._v("积分 "+s._s(s.info.total_credits))]):i("div",{staticClass:"r"},[s._v("￥"+s._s(s.info.total_amount))])]),s._v(" "),i("li",[i("div",{staticClass:"m"},[s._v("应付总额")]),s._v(" "),4==s.info.good_list[0].good_type||5==s.info.good_list[0].good_type?i("div",{staticClass:"r"},[i("span",{staticClass:"price"},[s._v("积分\n                        "),i("em",[s._v(s._s(s.info.total_credits))])])]):i("div",{staticClass:"r"},[i("span",{staticClass:"price"},[s._v("￥\n                        "),i("em",[s._v(s._s(s.info.total_amount))])])])])]),s._v(" "),i("div",{staticClass:"btn-wrap"},[i("div",{staticClass:"btn-fixed"},[i("button",{staticClass:"ui-btn ui-btn-block ui-btn-l2",attrs:{type:"button"},on:{click:function(t){s.save_data()}}},[s._v("提交")])])])]):s._e()])},o=[function(){var s=this,t=s.$createElement,i=s._self._c||t;return i("div",{staticClass:"title"},[i("div",[i("i",{staticClass:"iconfont icon-tanhao"})]),s._v(" "),i("div",[s._v(" 还没有添加收货人信息~ 点击添加")])])}],d={render:a,staticRenderFns:o};t.a=d}});