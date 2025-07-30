import {layoutNames} from "@/config/constants.js";
import EmptyLayout from "@/components/layouts/EmptyLayout.vue";
import DefaultLayout from "@/components/layouts/DefaultLayout.vue";
import AuthLayout from "@/components/layouts/AuthLayout.vue";

export default {
    [layoutNames.EmptyLayout]: EmptyLayout,
    [layoutNames.DefaultLayout]: DefaultLayout,
    [layoutNames.AuthLayout]: AuthLayout,
};
