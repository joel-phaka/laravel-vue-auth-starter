import {layoutNames} from "@/config/constants.js";
import {arrayOnlyIf} from "@/lib/utils.js";

const appFeatures = Object(window.__xapp__.features);

const authRoutes = [
    {
        path: '/signin',
        component: () => import('@/views/auth/SignIn.vue'),
        name: 'signin',
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Sign In',
            layout: layoutNames.AuthLayout,
            hideNavbar: true,
            hideFooter: true,
        },
    },
    ...arrayOnlyIf(appFeatures.user_registration, {
        path: '/signup',
        component: () => import('@/views/auth/SignUp.vue'),
        name: 'signup',
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Sign Up',
            layout: layoutNames.AuthLayout,
            hideNavbar: true,
            hideFooter: true,
        },
    }),
    ...arrayOnlyIf(appFeatures.user_registration, {
        path: '/verify',
        component: () => import('@/views/auth/Verify.vue'),
        name: 'verify',
        meta: {
            requireAuth: true,
            guestOnly: false,
            title: 'Verify Email',
            layout: layoutNames.EmptyLayout,
        },
    }),
    ...arrayOnlyIf(appFeatures.password_reset, {
        path: '/password/forgot',
        component: () => import('@/views/auth/ForgotPassword.vue'),
        name: 'forgot-password',
        props: true,
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Forgot Your Password?',
            layout: layoutNames.AuthLayout,
            hideNavbar: true,
            hideFooter: true,
        },
    }),
    ...arrayOnlyIf(appFeatures.password_reset, {
        path: '/password/reset/:token',
        component: () => import('@/views/auth/ResetPassword.vue'),
        name: 'reset-password',
        props: true,
        meta: {
            requireAuth: false,
            guestOnly: true,
            title: 'Reset Your Password',
            layout: layoutNames.AuthLayout
        },
    }),
];

export default authRoutes;
