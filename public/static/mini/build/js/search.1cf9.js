webpackJsonp([6],{"95K2":function(t,i,s){"use strict";var a=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"search-page"},[s("div",{staticClass:"search-history"},[s("div",{staticClass:"header"},[s("span",{staticClass:"title"},[t._v("最近搜索：")]),t._v(" "),t.$store.state.hList.length>0?s("a",{attrs:{herf:"javascript:;"},on:{click:t.clear}},[t._v("清空历史")]):t._e()]),t._v(" "),s("div",{staticClass:"h-list"},t._l(t.$store.state.hList,function(i){return s("span",{key:i,staticClass:"label",on:{click:function(s){t.search(i)}}},[t._v(t._s(i))])}))]),t._v(" "),1==t.showCat?s("div",{staticClass:"header-sort-mask",on:{click:t.closeDialog}}):t._e(),t._v(" "),s("div",{staticClass:"header-sort"},[s("div",{staticClass:"item",class:{active:t.showCat||t.cat_id},on:{click:function(i){t.showCat=!t.showCat}}},[s("i",{staticClass:"iconfont icon-fenlei"}),t._v(" "+t._s(t.cat_id?t.cat_list[t.cat_id].cat_name:"分类"))]),t._v(" "),s("div",{staticClass:"item sort",class:{active:t.sort},on:{click:t.onSort}},[s("i",{staticClass:"iconfont icon-paixu"}),t._v(" 排序")]),t._v(" "),s("transition",{attrs:{name:"cat"}},[s("div",{directives:[{name:"show",rawName:"v-show",value:t.showCat,expression:"showCat"}],staticClass:"class-dialog"},[s("div",{staticClass:"scroll"},[s("scroller",t._l(t.cat_list,function(i){return s("span",{key:i.id,class:{active:t.cat_id==i.id},on:{click:function(s){t.selectCat(i.id)}}},[t._v(t._s(i.cat_name)+"\n                            "),t.cat_id==i.id?s("i",{staticClass:"iconfont icon-dagou"}):t._e()])}))],1)])])],1),t._v(" "),s("ul",{staticClass:"thumb-box"},[s("li",[s("div",{staticClass:"thumb",on:{click:function(i){t.goto("/detail")}}},[s("img",{attrs:{src:"static/mini/img/demo/2.png"}})]),t._v(" "),s("div",{staticClass:"title",on:{click:function(i){t.goto("/detail")}}},[t._v("\n                诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通\n            ")]),t._v(" "),t._m(0)]),t._v(" "),s("li",[s("div",{staticClass:"thumb",on:{click:function(i){t.goto("/detail")}}},[s("img",{attrs:{src:"static/mini/img/demo/1.png"}})]),t._v(" "),s("div",{staticClass:"title",on:{click:function(i){t.goto("/detail")}}},[t._v("\n                德国 进口牛奶 欧德堡（Oldenburger）超高温处理全脂纯牛奶超高温处理全脂纯牛奶\n            ")]),t._v(" "),t._m(1)]),t._v(" "),s("li",[s("div",{staticClass:"thumb",on:{click:function(i){t.goto("/detail")}}},[s("img",{attrs:{src:"static/mini/img/demo/3.png"}})]),t._v(" "),s("div",{staticClass:"title",on:{click:function(i){t.goto("/detail")}}},[t._v("\n                维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售\n            ")]),t._v(" "),t._m(2)]),t._v(" "),s("li",[s("div",{staticClass:"thumb",on:{click:function(i){t.goto("/detail")}}},[s("img",{attrs:{src:"static/mini/img/demo/4.png"}})]),t._v(" "),s("div",{staticClass:"title",on:{click:function(i){t.goto("/detail")}}},[t._v("\n                联想(Lenovo)小新潮7000 13.3英寸超轻薄窄边框笔记本电脑(i\n            ")]),t._v(" "),t._m(3)])]),t._v(" "),t._m(4)])},c=[function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"info"},[s("span",{staticClass:"price"},[t._v("￥"),s("em",[t._v("390.00")])]),t._v(" "),s("i",{staticClass:"iconfont icon-gouwuche1"})])},function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"info"},[s("span",{staticClass:"jifen"},[t._v("积分 390")])])},function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"info"},[s("span",{staticClass:"price"},[t._v("￥"),s("em",[t._v("390.00")])]),t._v(" "),s("i",{staticClass:"iconfont icon-gouwuche1"})])},function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"info"},[s("span",{staticClass:"price"},[t._v("￥"),s("em",[t._v("5999.00")])]),t._v(" "),s("i",{staticClass:"iconfont icon-gouwuche1"})])},function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"nodata"},[s("i",{staticClass:"iconfont icon-tongyongmeiyoushuju"}),t._v(" "),s("div",[t._v("没有找到数据")])])}],n={render:a,staticRenderFns:c};i.a=n},"lvj/":function(t,i,s){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var a=s("o9Qd"),c=s("95K2"),n=s("VU/8"),e=n(a.a,c.a,null,null,null);i.default=e.exports},o9Qd:function(t,i,s){"use strict";var a=s(3);i.a={name:"search",mixins:[a.default],watch:{$route:function(){this.init()}},data:function(){return{showCat:!1,list:[],cat_id:"",sort:!1,cat_list:[{cat_name:"全部",id:0},{cat_name:"美白",id:1},{cat_name:"保养",id:2},{cat_name:"补水",id:3},{cat_name:"去角质",id:4},{cat_name:"精华素",id:5},{cat_name:"分类6",id:6},{cat_name:"分类7",id:7},{cat_name:"分类7",id:8},{cat_name:"分类7",id:9}]}},methods:{onSort:function(){this.sort=!this.sort},closeDialog:function(){this.showCat=!1},selectCat:function(t){this.cat_id=t},clear:function(){window.localStorage.removeItem("__SearchHistory__"),this.sethList([])},init:function(){this.getList(),setTimeout(function(){document.body.scrollTop=0},500)},getList:function(){var t=window.localStorage.getItem("__SearchHistory__");t&&(t=JSON.parse(t),this.sethList(t))}},mounted:function(){this.init(),this.setTitle("搜索")}}}});