import Vue from 'vue'
import Router from 'vue-router'



const noFound = () =>
  import ( /* webpackChunkName: "noFound" */ '@/components/noFound')



Vue.use(Router)
const router = new Router({
  routes: [{
      path: '/',
      name: 'home',
      component: noFound,
    },
    {
      path: '*',
      name: '404',
      component: noFound,


    },
  ]
})


export default router
