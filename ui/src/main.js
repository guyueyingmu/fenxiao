// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import vueResource from 'vue-resource'
import Global from './assets/js/common'

import store from './vuex'


Vue.use(vueResource);
Vue.http.options.emulateJSON = true;
Vue.config.devtools = true

// import Element from 'element-ui'
// Vue.use(Element)

import style from './assets/css/style.less'


import {
  Radio,
  RadioGroup,
  RadioButton,
  Checkbox,
  CheckboxGroup,
  Input,
  Form,
  FormItem,
  Select,
  Option,
  OptionGroup,
  Button,
  ButtonGroup,
  Switch,
  Dialog,
  Loading,
  MessageBox,
  Message,
  Popover,
  DatePicker,
  Row,
  Col,
  Icon,
  Menu,
  Submenu,
  MenuItem,
  Breadcrumb,
  BreadcrumbItem,
  Table,
  TableColumn,
  Alert,
  pagination
} from 'element-ui'

Vue.use(Radio)
Vue.use(Checkbox)
Vue.use(Switch)
Vue.use(RadioButton)
Vue.use(RadioGroup)
Vue.use(Breadcrumb)
Vue.use(BreadcrumbItem)


Vue.use(Menu)
Vue.use(Submenu)
Vue.use(MenuItem)

Vue.use(Table)
Vue.use(TableColumn)


Vue.use(CheckboxGroup)
Vue.use(OptionGroup)
Vue.use(ButtonGroup)
Vue.use(DatePicker)
// Vue.use(TimeSelect)
// Vue.use(TimePicker)
Vue.use(Row)
Vue.use(Col)
Vue.use(Icon)


Vue.use(Input)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Select)
Vue.use(Option)
Vue.use(Button)
Vue.use(Dialog)
Vue.use(Popover)
Vue.use(pagination)


Vue.use(Loading.directive)

Vue.prototype.$loading = Loading.service
Vue.prototype.$msgbox = MessageBox
Vue.prototype.$confirm = MessageBox.confirm
Vue.prototype.$alert = MessageBox.alert
Vue.prototype.$message = Message
Vue.config.productionTip = false
Vue.config.devtools = true;

/* eslint-disable no-new */
const Vm = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
