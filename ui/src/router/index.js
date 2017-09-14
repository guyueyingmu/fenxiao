import Vue from 'vue'
import Router from 'vue-router'



const Goods = () =>
  import ( /* webpackChunkName: "Goods" */ '@/components/goods/goods')
const Goods_add = () =>
  import ( /* webpackChunkName: "Goods_add" */ '@/components/goods/add')

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
      name: 'goods_edit',
      component: Goods_add
    },

  ]
})
//全局的 before 钩子
router.beforeEach((to,from,next) =>{
 
    next()
})

export default router
