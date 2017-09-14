import Vue from 'vue'
import Router from 'vue-router'



const Goods = () =>
  import ( /* webpackChunkName: "goods" */ '@/components/goods/goods')
  
const Goods_add = () =>
  import ( /* webpackChunkName: "goods_add" */ '@/components/goods/add')
  
const Goods_category = () =>
  import ( /* webpackChunkName: "goods_category" */ '@/components/goods/category')

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

  ]
})
//全局的 before 钩子
router.beforeEach((to,from,next) =>{
 
    next()
})

export default router
