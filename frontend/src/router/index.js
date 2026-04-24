import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../views/HomeView.vue'),
        },
        {
            path: '/product/:id',
            name: 'product-detail',
            component: () => import('../views/ProductDetailView.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/LoginView.vue'),
            meta: { guest: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/RegisterView.vue'),
            meta: { guest: true },
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('../views/CartView.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: () => import('../views/CheckoutView.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/admin',
            name: 'admin-dashboard',
            component: () => import('../views/admin/DashboardView.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: '/admin/products',
            name: 'admin-products',
            component: () => import('../views/admin/ProductsList.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: '/admin/products/create',
            name: 'admin-product-create',
            component: () => import('../views/admin/ProductForm.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: '/admin/products/edit/:id',
            name: 'admin-product-edit',
            component: () => import('../views/admin/ProductForm.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: '/admin/categories',
            name: 'admin-categories',
            component: () => import('../views/admin/CategoriesList.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();
    
    // Initialize user if token exists but user doesn't
    if (auth.token && !auth.user) {
        await auth.fetchUser();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && auth.isAuthenticated) {
        next({ name: 'home' });
    } else if (to.meta.requiresAdmin && !auth.isAdmin) {
        next({ name: 'home' });
    } else {
        next();
    }
});

export default router;
