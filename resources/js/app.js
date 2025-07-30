import './bootstrap';
import {createApp} from 'vue';
import {createPinia} from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import PrimeVue from 'primevue/config';
import App from '@/components/App.vue';
import {createHead} from "@vueuse/head";
import {VueRecaptchaPlugin} from "vue-recaptcha";
import router from "@/router/index.js";
import preset from "@/preset/index.js";


const app = createApp(App);

const head = createHead()
app.use(head);

app.use(VueRecaptchaPlugin, {
    v2SiteKey: import.meta.env.VITE_APP_RECAPTCHA_KEY
});

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);

app.use(PrimeVue, preset);

app.use(router);

app.mount('#app');
