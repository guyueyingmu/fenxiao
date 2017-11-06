// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
/* eslint-disable no-new */
import Vue from 'vue'
import App from './App'
import router from './router'
import vueResource from 'vue-resource'
import store from './vuex'

import Element from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import style from './assets/css/style.less'
Vue.use(Element, { size: 'small' })

Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.http.options.credentials = true;
Vue.config.devtools = true
Vue.config.productionTip = false


// 自定义指令
import directives from './directives'
Vue.directive('drop', directives.drop)
Vue.directive('autoPosition', directives.autoPosition)

// import ws from './ws'
// Vue.$ws = ws;
// window.$vue = Vue;


//全局的 before 钩子
router.beforeEach((to, from, next) => {
  store.state.c_loading = true

  function _check(menu) {
    let canView = false;
    var Reg = new RegExp(to.path)

    for (let i = 0; i < menu.length; i++) {
      let _break = false;
      for (let k = 0; k < menu[i].child.length; k++) {
        if (Reg.test(menu[i].child[k].link)) {
          canView = true;
          _break = true;
          break;
        } else {
          if (to.meta.role) {
            var Reg2 = new RegExp(to.meta.role)
            if (Reg2.test(to.path)) {
              canView = true;
              _break = true;
              break;
            } else {
              canView = false
            }
          } else {
            canView = false
          }


        }
      }
      if (_break) {
        break;
      }
    }
    return canView;
  }

  if (store.getters.nav_list.length > 0) {
    console.log('有nav_list')
    store.state.c_loading = false
    if (_check(store.getters.nav_list)) {
      next()

    } else {
      console.log('您没有权限访问！')
      store.state.RoseDialogVisible = true

      next(false)
    }
  } else {
    setTimeout(function () {
      if (_check(store.getters.nav_list)) {
        next()
        store.state.c_loading = false
      } else {
        console.log('您没有权限访问！')
        store.state.RoseDialogVisible = true

        next(false)
      }
    }, 500)
  }

})






const Vm = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
