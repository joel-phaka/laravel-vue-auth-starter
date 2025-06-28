import {layoutNames} from "@/config/constants.js";
import Empty from "@/components/layouts/Empty.vue";
import Default from "@/components/layouts/Default.vue";
import Auth from "@/components/layouts/Auth.vue";

export default {
    [layoutNames.Empty]: Empty,
    [layoutNames.Default]: Default,
    [layoutNames.Auth]: Auth,
};
