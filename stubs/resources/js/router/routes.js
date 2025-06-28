import {layoutNames} from "@/config/constants.js";
import config from "@/config/index.js";
import {routeGuards} from "@/router/guards.js";

const routes = [
    {
        path: '/',
        component: () => import('@/views/home/Index.vue'),
        name: 'home',
        meta: {
            requireAuth: true,
            guestOnly: false,
            title: 'Home',
            layout: layoutNames.Empty
        }
    },
    {
        path: '/about',
        component: () => import('@/views/home/About.vue'),
        meta: {
            requireAuth: false,
            guestOnly: false,
            title: 'About',
            layout: layoutNames.Empty
        }
    },
    {
        path: '/signin',
        component: () => import('@/views/auth/SignIn.vue'),
        name: 'signin',
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Sign In',
            layout: layoutNames.Auth
        },
    },
    {
        path: '/signup',
        component: () => import('@/views/auth/SignUp.vue'),
        name: 'signup',
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Sign Up',
            layout: layoutNames.Auth
        },
    },
    {
        path: '/verify',
        component: () => import('@/views/auth/Verify.vue'),
        name: 'verify',
        meta: {
            requireAuth: true,
            guestOnly: false,
            title: 'Verify Email',
            layout: layoutNames.Empty
        },
    },
    {
        path: '/password/forgot',
        component: () => import('@/views/auth/ForgotPassword.vue'),
        name: 'forgot-password',
        props: true,
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Forgot Your Password?',
            layout: layoutNames.Auth
        },
    },
    {
        path: '/password/reset/:token',
        component: () => import('@/views/auth/ResetPassword.vue'),
        name: 'reset-password',
        props: true,
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Reset Your Password',
            layout: layoutNames.Auth
        },
    },
    {
        path: '/test',
        component: () => import('@/views/test/Test.vue'),
        name: 'test',
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Test',
            layout: layoutNames.Empty
        },
    },
    {
        path: '/:pathMatch(.*)*',
        component: () => import('@/views/error/NotFound.vue'),
        name: 'not-found',
        meta: {
            requireAuth: false,
            guestOnly: false,
            title: 'Not Found',
            layout: layoutNames.Empty
        }
    }
].filter(route => {
    if (route.name === 'signup' && !config.allowSignUp) {
        return false;
    }

    return true;
}).map(route => {
    // Map route guards
    if (typeof routeGuards[route.name]?.beforeEnter === 'function') {
        route.beforeEnter = routeGuards[route.name].beforeEnter;
    }

    return route;
});

export default routes;
