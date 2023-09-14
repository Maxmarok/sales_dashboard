import { createWebHistory, createRouter } from "vue-router";
import Landing from '@/pages/Landing.vue'
import Reports from '@/pages/dashboard/reports/index.vue'
import AddStore from '@/pages/dashboard/stores/add.vue'
import Auth from '@/pages/auth/index.vue'
import Login from '@/pages/auth/login.vue'
import Register from '@/pages/auth/register.vue'
import Dashboard from '@/pages/dashboard/index.vue'

const routes = [
    {
        name: 'Landing',
        path: '/',
        component: Landing
    },
    {
        name: 'Dashboard',
        path: '/dashboard',
        component: Dashboard,
        children: [
            {
                name: 'Reports',
                path: 'reports',
                component: Reports
            },
            {
                name: 'Stores',
                path: 'stores',
                component: AddStore,
                children: [
                    {
                        name: 'AddStore',
                        path: 'add',
                        component: AddStore
                    },

                    {
                        name: 'ChangeStore',
                        path: 'change/:id?',
                        component: AddStore
                    },
                ]
            },
        ]
    },

    {
        name: 'Auth',
        path: '/auth',
        component: Auth,
        redirect: { name: 'Login' },
        children: [
            {
                name: 'Login',
                path: 'login',
                component: Login
            },
            {
                name: 'Register',
                path: 'register',
                component: Register
            },
        ]
    },

    {
        path: '/login',
        redirect: { name: 'Login' },
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router
  
