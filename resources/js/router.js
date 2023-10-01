import { createWebHistory, createRouter } from "vue-router";
import Landing from '@/pages/Landing.vue'
import Reports from '@/pages/dashboard/reports/index.vue'
import Finances from '@/pages/dashboard/finances/index.vue'
import Operations from '@/pages/dashboard/finances/operations.vue'
import Accounts from '@/pages/dashboard/finances/accounts.vue'
import Articles from '@/pages/dashboard/finances/articles.vue'
import Calendar from '@/pages/dashboard/calendar/index.vue'
import Stores from '@/pages/dashboard/stores/index.vue'
import StoreChange from '@/pages/dashboard/stores/change.vue'
import StoreAdd from '@/pages/dashboard/stores/add.vue'
import StoreList from '@/pages/dashboard/stores/list.vue'
import Auth from '@/pages/auth/index.vue'
import Login from '@/pages/auth/login.vue'
import Register from '@/pages/auth/register.vue'
import Dashboard from '@/pages/dashboard/index.vue'
import Desktop from '@/pages/dashboard/desktop.vue'

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
        redirect: { name: 'Desktop' },
        children: [
            {
                name: 'Desktop',
                path: 'desktop',
                component: Desktop,
            },
            {
                name: 'Reports',
                path: 'reports',
                component: Reports,
                children: [
                    {
                        name: 'ReportsItem',
                        path: ':id?',
                        component: Reports
                    },
                ],
            },
            {
                name: 'Stores',
                path: 'stores',
                component: Stores,
                redirect: { name: 'StoreList' },
                children: [
                    {
                        name: 'StoreAdd',
                        path: 'add',
                        component: StoreAdd
                    },

                    {
                        name: 'StoreList',
                        path: 'list',
                        component: StoreList
                    },

                    {
                        name: 'StoreChange',
                        path: 'change/:id?',
                        component: StoreChange
                    },
                ],
            },
            {
                name: 'Finances',
                path: 'finances',
                component: Finances,
                redirect: { name: 'Operations' },
                children: [
                    {
                        name: 'Operations',
                        path: 'operations',
                        component: Operations,
                    },
                    {
                        name: 'Accounts',
                        path: 'accounts',
                        component: Accounts,
                    },
                    {
                        name: 'Articles',
                        path: 'articles',
                        component: Articles,
                    },
                    
                ]
            },

            {
                name: 'Calendar',
                path: 'calendar',
                component: Calendar,
            }
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
  
