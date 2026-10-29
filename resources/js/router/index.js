import { createRouter, createWebHistory } from 'vue-router';
import LandingPage from '@/views/LandingPage.vue';
import Dashboard from '@/views/Dashboard.vue';

const routes = [
    {
        path: '/',
        name: 'landing',
        component: LandingPage,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else if (to.hash) {
            return {
                el: to.hash,
                behavior: 'smooth',
            };
        } else {
            return { top: 0, behavior: 'smooth' };
        }
    },
});

// Guard perlindungan rute yang memerlukan otentikasi (meta: { requiresAuth: true })
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('nodus_token');
    if (to.meta.requiresAuth && !token) {
        // Arahkan ke beranda dengan parameter login
        next({ path: '/', query: { login: '1', redirect: to.fullPath } });
    } else {
        next();
    }
});

export default router;
