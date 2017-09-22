// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import vueResource from 'vue-resource'

import store from './vuex'


Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.config.devtools = true

import Element from 'element-ui'
Vue.use(Element)


import 'element-ui/lib/theme-default/index.css'
import style from './assets/css/style.less'


// import {
//   Radio,
//   RadioGroup,
//   RadioButton,
//   Checkbox,
//   //   CheckboxGroup,
//   Input,
//   Form,
//   FormItem,
//   Select,
//   Option,
//   //   OptionGroup,
//   Button,
//   //   ButtonGroup,
//   //   Switch,
//   Dialog,
//   Loading,
//   MessageBox,
//   Message,
//   Tooltip,
//   //   DatePicker,
//   Row,
//   Col,
//   Icon,
//   Menu,
//   Submenu,
//   MenuItem,
//   Breadcrumb,
//   BreadcrumbItem,
//   Table,
//   TableColumn,
//   Alert,
//   pagination,
//   Tabs,
//   TabPane
//   //   Upload
// } from 'element-ui'

// Vue.use(Radio)
// Vue.use(Checkbox)
// // Vue.use(Switch)
// Vue.use(RadioButton)
// Vue.use(RadioGroup)
// Vue.use(Breadcrumb)
// Vue.use(BreadcrumbItem)


// Vue.use(Menu)
// Vue.use(Submenu)
// Vue.use(MenuItem)

// Vue.use(Table)
// Vue.use(TableColumn)


// // Vue.use(CheckboxGroup)
// // Vue.use(OptionGroup)
// // Vue.use(ButtonGroup)
// // Vue.use(DatePicker)
// // Vue.use(TimeSelect)
// // Vue.use(TimePicker)
// Vue.use(Row)
// Vue.use(Col)
// Vue.use(Icon)


// Vue.use(Input)
// Vue.use(Form)
// Vue.use(FormItem)
// Vue.use(Select)
// Vue.use(Option)
// Vue.use(Button)
// Vue.use(Dialog)
// Vue.use(Tooltip)
// Vue.use(pagination)
// Vue.use(Tabs)
// Vue.use(TabPane)
// // Vue.use(Upload)



// Vue.use(Loading.directive)

// Vue.prototype.$loading = Loading.service
// Vue.prototype.$msgbox = MessageBox
// Vue.prototype.$confirm = MessageBox.confirm
// Vue.prototype.$alert = MessageBox.alert
// Vue.prototype.$message = Message
// Vue.prototype.$prompt = MessageBox.prompt
Vue.config.productionTip = false

// Vue.config.devtools = true;

/* eslint-disable no-new */


//全局的 before 钩子
router.beforeEach((to, from, next) => {

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
      } else {
        console.log('您没有权限访问！')
        store.state.RoseDialogVisible = true
        next(false)
      }
    }, 500)
  }







  //   let menu = window.localStorage.getItem('__Menu__')

  //   if (menu) {
  //       console.log('2')
  //     if (_check(menu)) {
  //         console.log('bb')
  //       next()
  //     } else {
  //       alert('无权访问')
  //     }

  //   } else {
  //     console.log('4')
  //     next(false)


  //   }





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
