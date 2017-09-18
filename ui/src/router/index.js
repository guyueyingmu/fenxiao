import Vue from 'vue'
import Router from 'vue-router'



const Goods = () =>
  import ( /* webpackChunkName: "goods" */ '@/components/goods/goods')

const Goods_add = () =>
  import ( /* webpackChunkName: "goods_add" */ '@/components/goods/add')

const Goods_category = () =>
  import ( /* webpackChunkName: "goods_category" */ '@/components/goods/category')

const Order_list = () =>
  import ( /* webpackChunkName: "order_list" */ '@/components/order/list')

const OrderDetail = () =>
  import ( /* webpackChunkName: "OrderDetail" */ '@/components/order/detail')

const Users = () =>
  import ( /* webpackChunkName: "users" */ '@/components/users/users')

const Withdraw = () =>
  import ( /* webpackChunkName: "withdraw" */ '@/components/users/withdraw')

const Refund = () =>
  import ( /* webpackChunkName: "refund" */ '@/components/order/refund') //退款

const Signin = () =>
  import ( /* webpackChunkName: "signin" */ '@/components/users/signin')

const Exchange = () =>
  import ( /* webpackChunkName: "exchange" */ '@/components/order/exchange')

const Goodcomment = () =>
  import ( /* webpackChunkName: "goodcomment" */ '@/components/order/goodcomment')

Vue.use(Router)
const router = new Router({
  routes: [{
      path: '/',
      name: 'home',
      redirect: {
        name: 'Goods'
      }
    },
    {
      path: '/goods',
      name: 'Goods',
      component: Goods
    },
    {
      path: '/goods_add',
      name: 'Goods_add',
      component: Goods_add
    },
    {
      path: '/goods_edit/id/:id',
      name: 'Goods_edit',
      component: Goods_add
    },
    {
      path: '/goods_category',
      name: 'Goods_category',
      component: Goods_category
    },
    {
      path: '/orders',
      name: 'Order',
      component: Order_list
    },
    {
      path: '/users',
      name: 'Users',
      component: Users
    },
    {
      path: '/withdraw',
      name: 'Withdraw',
      component: Withdraw
    },
    {
      path: '/order_detail/order_id/:order_id',
      name: 'OrderDetail',
      component: OrderDetail
    },
    {
      path: '/refund', //退款
      name: 'Refund',
      component: Refund
    },
    {
      path: '/signin',
      name: 'Signin',
      component: Signin
    },
    {
      path: '/exchange',
      name: 'Exchange',
      component: Exchange
    },
    {
      path: '/goods_comments',
      name: 'Goodcomment',
      component: Goodcomment
    },

  ]
})
//全局的 before 钩子
router.beforeEach((to, from, next) => {

  next()
})

export default router
