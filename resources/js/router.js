import { createWebHistory, createRouter } from "vue-router";
import Landing from '@/pages/Landing.vue'
import Reports from '@/pages/Reports.vue'
import Login from '@/pages/Login.vue'

const routes = [
    {
        name: 'Landing',
        path: '/',
        component: Landing
    },
    {
        name: 'Reports',
        path: '/reports',
        component: Reports
    },
    {
        name: 'Login',
        path: '/login',
        component: Login
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router
  
