import {layoutNames} from "@/config/constants.js";
import config from "@/config/index.js";
import {routeGuards} from "@/router/guards/index.js";
import authRoutes from "@/router/routes/auth.routes.js";
import defaultRoutes from "@/router/routes/default.routes.js";

const routes = [
    ...defaultRoutes,
    ...authRoutes,
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
