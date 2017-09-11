import Vue from 'vue'
import Router from 'vue-router'

const Home = () =>
  import ( /* webpackChunkName: "Home" */ '@/components/Home')
const Table = () =>
  import ( /* webpackChunkName: "Table" */ '@/components/Table')
const Form = () =>
  import ( /* webpackChunkName: "Form" */ '@/components/Form')

const Goods = () =>
  import ( /* webpackChunkName: "Goods" */ '@/components/goods/goods')
const Goods_add = () =>
  import ( /* webpackChunkName: "Goods_add" */ '@/components/goods/add')

Vue.use(Router)

export default new Router({
  routes: [{
      path: '/',
      name: 'Home',
      component: Home
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
      path: '/Table',
      name: 'Table',
      component: Table
    },
    {
      path: '/Form',
      name: 'Form',
      component: Form
    }
  ]
})
