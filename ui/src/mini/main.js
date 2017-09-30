// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import vueResource from 'vue-resource'

import store from './vuex'

// 按需引入部分组件
import {
  Toast,
  MessageBox
} from 'mint-ui'
Vue.component(Toast.name, Toast);
Vue.component(MessageBox.name, MessageBox);


Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.config.devtools = true
// import 'mint-ui/lib/style.css'
import style from './assets/css/style.less'
import VueScroller from 'vue-scroller'
Vue.use(VueScroller)

const IS = {}
IS.IPhone = /iPhone/.test(navigator.userAgent);
IS.Android = /Android/.test(navigator.userAgent);
IS.WeiXin = /MicroMessenger/.test(navigator.userAgent);
IS.PC = /Win32/.test(navigator.platform);
Vue.prototype.$is = IS

Vue.config.productionTip = false
Vue.config.devtools = true;

Vue.prototype.$msg = function (str, time) {
  Toast({
    message: str,
    // position: 'bottom',
    duration: time || 3000
  });
}

Vue.prototype.$confirm = function (opt,cb) {
    MessageBox({
        title: opt.title||'提示',
        message: opt.msg,
        showCancelButton: true
      }).then(function(){
          if(typeof cb == 'function'){
              cb()
          }
      });
  }

/* eslint-disable no-new */


// //全局的 before 钩子
// router.beforeEach((to, from, next) => {

//   function _check(menu) {
//     let canView = false;
//     var Reg = new RegExp(to.path)

//     for (let i = 0; i < menu.length; i++) {
//       let _break = false;
//       for (let k = 0; k < menu[i].child.length; k++) {
//         if (Reg.test(menu[i].child[k].link)) {
//           canView = true;
//           _break = true;
//           break;
//         } else {
//           if (to.meta.role) {
//             var Reg2 = new RegExp(to.meta.role)

//             if (Reg2.test(to.path)) {
//               canView = true;
//               _break = true;
//               break;
//             } else {
//               canView = false
//             }
//           } else {
//             canView = false
//           }


//         }
//       }
//       if (_break) {
//         break;
//       }
//     }
//     return canView;
//   }

//   if (store.getters.nav_list.length > 0) {
//     if (_check(store.getters.nav_list)) {
//       next()
//     } else {
//       console.log('您没有权限访问！')
//       store.state.RoseDialogVisible = true
//       next(false)
//     }
//   } else {
//     setTimeout(function () {
//       if (_check(store.getters.nav_list)) {
//         next()
//       } else {
//         console.log('您没有权限访问！')
//         store.state.RoseDialogVisible = true
//         next(false)
//       }
//     }, 500)
//   }


// })
const loading = {
  a: '',
  inserted(el, binding) {
    let html = '<div class="spin-mask"></div><div class="spin-main">\
                <div class="loader">\
                    <svg viewBox="25 25 50 50" class="circular">\
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10" class="path"></circle>\
                    </svg>\
                </div>\
                <div class="spin-text">加载中..</div>\
            </div>'
    let d = document.createElement("DIV")
    if (binding.modifiers.full) {
      d.className = 'load full'
      el.style.position = 'relative'
    } else {
      d.className = 'load'
    }
    d.style.display = 'none'
    d.innerHTML = html;
    el.appendChild(d)
    binding.def.a = d;
    // console.log('inserted:', binding.def)

  },
  update(el, binding) {
    // console.log('update:', binding.def)
    if (binding.value) {
      binding.def.a.style.display = 'block'
    } else {
      binding.def.a.style.display = 'none'
    }


  }
}

const focus = {
  inserted: function (el) {
    el.focus()
  }
}

Vue.directive('loading', loading)
Vue.directive('focus', focus)


const Vm = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
