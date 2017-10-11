import Vue from 'vue'
import Router from 'vue-router'


const home = () =>
  import ( /* webpackChunkName: "home" */ '@/components/home')

const search = () =>
  import ( /* webpackChunkName: "search" */ '@/components/search')

const productClass = () =>
  import ( /* webpackChunkName: "class" */ '@/components/class')
const cart = () =>
  import ( /* webpackChunkName: "cart" */ '@/components/cart')

const userCenter = () =>
  import ( /* webpackChunkName: "userCenter" */ '@/components/userCenter')

const detail = () =>
  import ( /* webpackChunkName: "detail" */ '@/components/detail')

const reg = () =>
  import ( /* webpackChunkName: "reg" */ '@/components/register')

const confirm = () =>
  import ( /* webpackChunkName: "confirm" */ '@/components/confirmOrder')
  
  const pay = () =>
    import ( /* webpackChunkName: "pay" */ '@/components/payOrder')

const address = () =>
  import ( /* webpackChunkName: "address" */ '@/components/address')
const addAddress = () =>
  import ( /* webpackChunkName: "addAddress" */ '@/components/addAddress')
const success = () =>
  import ( /* webpackChunkName: "success" */ '@/components/paySuccess')
const fail = () =>
  import ( /* webpackChunkName: "fail" */ '@/components/payfail')
const talk = () =>
  import ( /* webpackChunkName: "talk" */ '@/components/talk')

const tixian = () =>
  import ( /* webpackChunkName: "tixian" */ '@/components/tixian')


const favorite = () =>
  import ( /* webpackChunkName: "favorite" */ '@/components/favorite')

const history = () =>
  import ( /* webpackChunkName: "history" */ '@/components/history')
const order = () =>
  import ( /* webpackChunkName: "order" */ '@/components/order')


const orderDetail = () =>
  import ( /* webpackChunkName: "orderDetail" */ '@/components/order-detail')

const start = () =>
  import ( /* webpackChunkName: "start" */ '@/components/start')



const shouhou = () =>
  import ( /* webpackChunkName: "shouhou" */ '@/components/shouhou')

const noFound = () =>
  import ( /* webpackChunkName: "noFound" */ '@/components/noFound')

Vue.use(Router)
const router = new Router({
  routes: [{
      path: '/',
      name: 'home',
      component: home,
    },
    {
      path: '/search',
      name: 'search',
      component: search,
    },
    {
      path: '/search/cat_id/:cat_id',
      name: 'search2',
      component: search,
    },
    {
      path: '/class',
      name: 'class',
      component: productClass,
    },
    {
      path: '/cart',
      name: 'cart',
      component: cart,
    },
    {
      path: '/userCenter',
      name: 'userCenter',
      component: userCenter,
    },
    {
      path: '/detail/id/:id',
      name: 'detail',
      component: detail,
    },
    {
      path: '/confirm',
      name: 'confirm',
      component: confirm,
    },
    {
      path: '/pay/order_id/:order_id',
      name: 'pay',
      component: pay,
    },
    {
      path: '/reg',
      name: 'reg',
      component: reg,
    },
    {
      path: '/address',
      name: 'address',
      component: address,
    },
    {
      path: '/address/is_use/:is_use',
      name: 'address2',
      component: address,
    },
    {
      path: '/add_address',
      name: 'addAddress',
      component: addAddress,
    },
    {
      path: '/edit_address/id/:id',
      name: 'editAddress',
      component: addAddress,
    },
    {
      path: '/success',
      name: 'success',
      component: success,
    },
    {
      path: '/fail',
      name: 'fail',
      component: fail,
    },
    {
      path: '/talk',
      name: 'talk',
      component: talk,
    },
    {
      path: '/tixian',
      name: 'tixian',
      component: tixian,
    },
    {
      path: '/history',
      name: 'history',
      component: history,
    },
    {
      path: '/favorite',
      name: 'favorite',
      component: favorite,
    },
    {
      path: '/order',
      name: 'order',
      component: order,
    },
    {
      path: '/order_detail',
      name: 'orderDetail',
      component: orderDetail,
    },
    {
      path: '/shouhou',
      name: 'shouhou',
      component: shouhou,
    },
    {
      path: '/start',
      name: 'start',
      component: start,
    },
    {
      path: '*',
      name: '404',
      component: noFound,


    },
  ]
})


export default router
