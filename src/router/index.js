import Vue from 'vue'
import VueRouter from 'vue-router'
import Search from '../views/Search.vue'

Vue.use(VueRouter)

  const routes = [
  {
    path: '/',
    alias: '/search',
    name: 'Search',
    component: Search
  },
  {
    path: '/search/:query',
    name: 'Search',
    component: Search
  },

  {
    path: '/about',
    name: 'About',

    component: () => import('../views/About.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
