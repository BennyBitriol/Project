import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'
Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'mainpage',
      component: () => import('@/views/MainPage.vue'),
      // beforeEnter: (to, from, next) => {
      //   if (!store.getters['auth/authenticated']){
      //    return next({
      //      name: 'login'
      //     })
      //   }
      //   next()
      // },
    },
    {
      path: '/room',
      name: 'room',
      component: () => import('@/views/Room.vue'),
      // beforeEnter: (to, from, next) => {
      //   if (!store.getters['auth/authenticated']){
      //    return next({
      //      name: 'login'
      //     })
      //   }
      //   next()
      // }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/Login.vue'),
      // beforeEnter: (to, from, next) => {
      //   if (store.getters['auth/authenticated']){
      //    return next({
      //      name: 'dashboard'
      //     })
      //   }
      //   next()
      // },
      meta: {
        layout: 'full',
      },
    },
    {
      path: '/error-404',
      name: 'error-404',
      component: () => import('@/views/error/Error404.vue'),
      meta: {
        layout: 'full',
      },
    },
    {
      path: '*',
      redirect: 'error-404'
    },
    {
      path: '/statistic/:id',
      name: 'statistic',
      component: () => import('@/views/Statistic.vue')
    },
    {
      path: '/allroom',
      name: 'allroom',
      component: () => import('@/views/AllRoom.vue')
    },
    {
      path: '/devicedetail/:id',
      name: 'devicedetail',
      component: () => import('@/views/DeviceDetail.vue')
    },
    {
      path: '/energyreport/:type/:id',
      name: 'energyreport',
      component: () => import('@/views/EnergyReport.vue')
    },
    {
      path: '/weatherdetail/:type/:id',
      name: 'weatherdetail',
      component: () => import('@/views/WeatherDetail.vue')
    },
    // {
    //   path: '/viewroom/:id',
    //   name: 'viewroom',
    //   component: () => import('@/views/ViewRoom.vue')
    // },
    {
      path: '/viewroom/',
      name: 'viewroom',
      component: () => import('@/views/ViewRoom.vue')
    },
    {
      path: '/login/line/callback',
      name: 'loginline',
      component: () => import('@/views/LoginLine.vue')
    },
    {
      path: '/challenge',
      name: 'challenge',
      component: () => import('@/views/Challenge.vue')
    },
    {
      path: '/test',
      name: 'test',
      component: () => import('@/views/Test.vue')
    },
    {
      path: '/presentcontroller',
      name: 'presentcontroller',
      component: () => import('@/views/PresentController.vue')
    }
    // {   
    //     path:'/devicedetails/control',
    //     name: 'control',
    //     component: () => import('@/views/DeviceDetails/Control.vue'),
    // },
    // {   
    //   path:'/devicedetails/statistic',
    //     name: 'statistic',
    //     component: () => import('@/views/DeviceDetails/Statistic.vue'),
    // },
    // {
    //   path:'/devicedetails/timer',
    //     name: 'timer',
    //     component: () => import('@/views/DeviceDetails/Timer.vue'),
    // },
  ],
})

export default router
