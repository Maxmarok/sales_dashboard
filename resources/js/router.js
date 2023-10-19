import { createWebHistory, createRouter } from "vue-router"
import store from '@/store'
import Landing from '@/pages/Landing.vue'
import Reports from '@/pages/dashboard/reports/index.vue'
import Cashflow from '@/pages/dashboard/reports/cashflow.vue'
import Pnl from '@/pages/dashboard/reports/pnl.vue'
import Expenses from '@/pages/dashboard/reports/expenses.vue'
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

import auth from '@/middleware/auth'
import middlewarePipeline from "@/middleware/middleware-pipeline"

const routes = [
    {
        name: 'Landing',
        path: '/',
        component: Landing,
        redirect: { name: 'Dashboard' },
    },
    {
        name: 'Dashboard',
        path: '/dashboard',
        component: Dashboard,
        meta: {
            middleware: auth
        },
        redirect: { name: 'Desktop' },
        children: [
            {
                name: 'Desktop',
                path: 'desktop',
                component: Desktop,
                // meta: {
                //     middleware: auth
                // },
            },
            {
                name: 'Reports',
                path: 'reports',
                component: Reports,
                redirect: { name: 'Cashflow' },
                children: [
                    {
                        name: 'Cashflow',
                        path: 'cashflow',
                        component: Cashflow
                    },

                    {
                        name: 'Pnl',
                        path: 'pnl',
                        component: Pnl
                    },

                    {
                        name: 'Expenses',
                        path: 'expenses',
                        component: Expenses
                    },
                ]
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
            {
                name: 'Logout',
                path: 'logout',

                redirect: to => {
                    store.dispatch("logout")
                    return { name: 'Login'}
                }

            }
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

router.beforeEach((to, from, next) => {

    if (!to.meta.middleware) {
        return next()
    }
    
    const middleware = Array.isArray(to.meta.middleware)
    ? to.meta.middleware
    : [to.meta.middleware];

    const context = {
        to,
        from,
        next,
        store
    };

    return middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1),
    });
})

export default router
  
