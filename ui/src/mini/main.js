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
import { InfiniteScroll,Lazyload,Spinner,Indicator } from 'mint-ui';
Vue.use(Lazyload);
Vue.use(InfiniteScroll);
Vue.component(Spinner.name, Spinner);

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
    // window.document.body.scrollTop = 0
})


const loading = {
  inserted(el, binding) {

  },
  update(el, binding) {
    // console.log('update:', binding.def)
    if (binding.value) {
        Indicator.open('加载中...');
    } else {
        Indicator.close();
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
