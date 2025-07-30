import {createRouter, createWebHistory} from "vue-router";
import routes from "@/router//routes";
import beforeEach from "@/router/guards/before-each.js";

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(beforeEach);

export default router;
