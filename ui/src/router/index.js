import Vue from 'vue'
import Router from 'vue-router'



const Goods = () =>
  import( /* webpackChunkName: "goods" */ '@/components/goods/goods')

const Goods_add = () =>
  import( /* webpackChunkName: "goods_add" */ '@/components/goods/add')

const Goods_category = () =>
  import( /* webpackChunkName: "goods_category" */ '@/components/goods/category')

const Order_list = () =>
  import( /* webpackChunkName: "order_list" */ '@/components/order/list')

const OrderDetail = () =>
  import( /* webpackChunkName: "OrderDetail" */ '@/components/order/detail')

const Users = () =>
  import( /* webpackChunkName: "users" */ '@/components/users/users')

const Withdraw = () =>
  import( /* webpackChunkName: "withdraw" */ '@/components/users/withdraw')

const Refund = () =>
  import( /* webpackChunkName: "refund" */ '@/components/order/refund') //退款

const Signin = () =>
  import( /* webpackChunkName: "signin" */ '@/components/users/signin')

const Exchange = () =>
  import( /* webpackChunkName: "exchange" */ '@/components/order/exchange')

const Goodcomment = () =>
  import( /* webpackChunkName: "goodcomment" */ '@/components/order/goodcomment')

const Credits = () =>
  import( /* webpackChunkName: "credits" */ '@/components/users/credits')

const Score = () =>
  import( /* webpackChunkName: "score" */ '@/components/users/score')

const DisGoods = () =>
  import( /* webpackChunkName: "disGoods" */ '@/components/dis/dis_log')

const OrderdisLog = () =>
  import( /* webpackChunkName: "orderdisLog" */ '@/components/dis/dis_log')

const Userdis = () =>
  import( /* webpackChunkName: "userdis" */ '@/components/dis/distributor')


const UserdisMember = () =>
  import( /* webpackChunkName: "userdisMember" */ '@/components/dis/dis_users')

const disSet = () =>
  import( /* webpackChunkName: "disSet" */ '@/components/dis/dis_set')

const disMember = () =>
  import( /* webpackChunkName: "disMember" */ '@/components/dis/dis_member')

const UserRole = () =>
  import( /* webpackChunkName: "UserRole" */ '@/components/system/user_role')


const Manager = () =>
  import( /* webpackChunkName: "manager" */ '@/components/system/manager')

const Log = () =>
  import( /* webpackChunkName: "log" */ '@/components/system/log')

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
    path: '/goods/goods_add',
    name: 'Goods_add',
    component: Goods_add
  },
  {
    path: '/goods/goods_edit/id/:id',
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
    path: '/orders/order_id/:order_id',
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
  {
    path: '/dis_goods',
    name: 'DisGoods',
    component: DisGoods
  },
  {
    path: '/score_list',
    name: 'Credits',
    component: Credits
  },
  {
    path: '/score',
    name: 'Score',
    component: Score
  },
  {
    path: '/dis_log', //分成日志
    name: 'OrderdisLog',
    component: OrderdisLog
  },
  {
    path: '/dis_users', //分销会员
    name: 'UserdisMember',
    component: UserdisMember
  },
  {
    path: '/distributor', //分销商
    name: 'Userdis',
    component: Userdis
  },
  {
    path: '/dis_config/c_type/:c_type', //分销设置
    name: 'disSet',
    component: disSet
  },
  {
    path: '/set/c_type/:c_type', //积分设置
    name: 'disSet',
    component: disSet
  },
  {
    path: '/user_role', //角色管理
    name: 'UserRole',
    component: UserRole
  },


  {
    path: '/manager', //管理员
    name: 'Manager',
    component: Manager
  },
  {
    path: '/log', //日志
    name: 'Log',
    component: Log
  },
  {
    path: '/distributor/user_id/:user_id/level/:level', //分销商-我的会员
    name: 'disMember',
    component: disMember
  },
  ]
})
//全局的 before 钩子
router.beforeEach((to, from, next) => {

  next()
})

export default router
