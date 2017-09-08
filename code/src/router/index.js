import Vue from 'vue'
import Router from 'vue-router'

const Home = () => import(/* webpackChunkName: "Home" */ '@/components/Home')
const Table = () => import(/* webpackChunkName: "Table" */ '@/components/Table')
const Form = () => import(/* webpackChunkName: "Form" */ '@/components/Form')

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
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
