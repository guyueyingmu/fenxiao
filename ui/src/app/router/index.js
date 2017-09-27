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
      path: '/detail',
      name: 'detail',
      component: detail,
    },
    {
      path: '*',
      name: '404',
      component: noFound,


    },
  ]
})


export default router
