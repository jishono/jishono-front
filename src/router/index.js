import { createRouter, createWebHistory } from 'vue-router';
import Search from '../views/Search.vue';


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
    component: Search
  },
  {
    path: '/:pathMatch(.*)*',
    component: Search
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
