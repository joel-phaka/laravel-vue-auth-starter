import {layoutNames} from "@/config/constants.js";


const defaultRoutes = [
    {
        path: '/',
        component: () => import('@/views/home/Index.vue'),
        name: 'home',
        meta: {
            requireAuth: true,
            guestOnly: false,
            title: 'Home',
            layout: layoutNames.DefaultLayout
        }
    },
    {
        path: '/about',
        component: () => import('@/views/home/About.vue'),
        name: 'about',
        meta: {
            requireAuth: false,
            guestOnly: false,
            title: 'About',
            layout: layoutNames.DefaultLayout
        }
    }
];

export default defaultRoutes;
