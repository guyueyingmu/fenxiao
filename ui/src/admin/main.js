// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import vueResource from 'vue-resource'

import store from './vuex'


Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.http.options.credentials = true;
Vue.config.devtools = true

import Element from 'element-ui'
Vue.use(Element)


import 'element-ui/lib/theme-default/index.css'
import style from './assets/css/style.less'


Vue.config.productionTip = false

// Vue.config.devtools = true;

/* eslint-disable no-new */


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

const drop = {
  x: 0,
  y: 0,
  canMove: false,
  inserted(el, binding) {
    let that = binding.def;
    let offsetX =0;

    el.addEventListener('mousedown', function (e) {

      if(e.target && e.target.id == 'move_head' || e.target.nodeName == "B" && e.target.offsetParent.id == 'move_head'){
          if(e.target.id == 'move_head'){
            offsetX =el.offsetWidth -  e.target.offsetWidth;
          }
          if(e.target.nodeName == "B" && e.target.offsetParent.id == 'move_head'){
            offsetX =el.offsetWidth -  e.target.offsetParent.offsetWidth;
          }
        that.x = e.offsetX;
        that.y = e.offsetY;
        el.style.top = el.offsetTop + 'px';
        el.style.left = el.offsetLeft + 'px';
        that.canMove = true;
        document.addEventListener('mousemove', _move, false)
      }
     
    }, false)
    function _move (e){
  
        if (that.canMove) {
            el.style.top = (e.clientY - that.y) + 'px';
            el.style.left = (e.clientX - that.x  - offsetX) + 'px';
          }
    }

    document.addEventListener('mouseup', function (e) {
      that.canMove = false;
      document.removeEventListener('mousemove',_move)
    }, false)

  },
}

const autoPosition ={
    inserted(el, binding) {
        let that = binding.def;
        let className = el.className;
        console.log(className)
        let _array = document.getElementsByClassName('reply-min-box'),top=0;
        if(_array.length > 0){
            let _h = _array.length >1? 36:32;
            top  =  _h * (_array.length -1);
       
        }
        el.style.top = top +'px'

    }

}

Vue.directive('drop', drop)
Vue.directive('autoPosition', autoPosition)

const Vm = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
