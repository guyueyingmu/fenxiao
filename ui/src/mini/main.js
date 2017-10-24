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
  MessageBox,
  InfiniteScroll,Lazyload,Spinner
} from 'mint-ui'
Vue.component(Toast.name, Toast);
Vue.component(MessageBox.name, MessageBox);
Vue.use(Lazyload);
Vue.use(InfiniteScroll);
Vue.component(Spinner.name, Spinner);

Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.http.interceptors.push((request, next) => {
    var timeout;
	// 這裡改用 _timeout ，就不會觸發原本的
    if (request._timeout) {
    	// 一樣綁定一個定時器，但是這裡是只要觸發了，就立即返回 response ， 並且這邊自定義了 status 和 statusText
        timeout = setTimeout(() => {
            if(request.onTimeout) request.onTimeout(request)
			request.abort()
        }, request._timeout);
    }

    next((response) => {
        clearTimeout(timeout);
    });
})

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

Vue.prototype.$confirm = function (opt) {
  if (!opt) {
    return
  }
  MessageBox({
    title: opt.title || '提示',
    message: opt.msg,
    showCancelButton: true
  }).then(function (action) {
    if (action == 'confirm') {
      if (typeof opt.yes == 'function') {
        opt.yes()
      }
    }
    if (action == 'cancel') {
      if (typeof opt.no == 'function') {
        opt.no()
      }
    }

  });
}
Vue.prototype.$alert = function (msg, cb) {
  if (!msg) {
    return
  }
  MessageBox({
    message: msg,
    showCancelButton: false
  }).then(function (action) {

    if (action == 'confirm') {
      if (typeof cb == 'function') {
        cb()
      }
    }


  });
}

/* eslint-disable no-new */


//全局的 before 钩子
router.beforeEach((to, from, next) => {
    next()
    window.document.body.scrollTop = 0
})


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
    } else if (binding.modifiers.win) {
      d.className = 'load win'
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
  inserted(el, binding) {
    el.focus();
  },
  update(el, binding) {
    el.focus();
  }
}

Vue.directive('focusd', focus)
Vue.directive('loading', loading)



const Vm = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
