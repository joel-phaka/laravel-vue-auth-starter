import {createRouter, createWebHistory} from "vue-router";
import routes from "@/router/routes.js";
import {beforeEach} from "@/router/guards.js";

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(beforeEach);

export default router;
