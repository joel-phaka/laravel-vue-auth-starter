<script setup>
import {shallowRef, provide, ref, computed, watch, onMounted, onBeforeUnmount} from "vue";
import {useRouter, useRoute} from "vue-router";
import {storeToRefs} from "pinia";
import { useRecaptchaProvider } from "vue-recaptcha";
import layouts from "@/components/layouts/index.js";
import browserStorage from "@/lib/browser-storage.js";
import {useAuthStore} from "@/stores/auth.store";

useRecaptchaProvider();

const route = useRoute();
const router = useRouter();

const layout = shallowRef('div');
const themeMode = ref(['light', 'dark'].includes(browserStorage.get("themeMode")) ? browserStorage.get("themeMode") : 'light');
const isDarkMode = ref(themeMode.value === 'dark');
const isUpdatingThemeMode = ref(false);

const authStore = useAuthStore();
const {isLoggedIn} = storeToRefs(authStore);

const setLayout = (layoutName) => {
    layout.value = layouts[layoutName] || 'div';
};

const toggleThemeMode = async () => {
    if (isUpdatingThemeMode.value) return;

    isDarkMode.value = document.documentElement.classList.toggle('dark-mode');

    browserStorage.set('themeMode', isDarkMode.value ? 'dark' : 'light');

    isUpdatingThemeMode.value = false;
};

provide("app:setLayout", setLayout);
provide('app:theme', {
    themeMode: computed(() => themeMode.value),
    themeColor: computed(() => isDarkMode.value ? '#000000' : '#FFFFFF'),
    themeColorInverted: computed(() => isDarkMode.value ? '#FFFFFF' : '#000000'),
    isDarkMode: computed(() => isDarkMode.value),
    isUpdatingThemeMode: computed(() => isUpdatingThemeMode.value),
    toggleThemeMode
});

router.afterEach((to) => {
    setLayout(to.meta.layout);

    if (to.meta.title) document.title = `${to.meta.title} / ${import.meta.env.VITE_APP_APP_NAME ?? 'App'}`;
});

watch(
    () => isLoggedIn.value,
    (value) => {
        let query = {};

        if (!/\/(signin|signup)?/.test(route.path)) {
            query.return_url = route.meta.url;
        }

        if (!value) {
            router.replace({path: '/signin', query});
        }
    }
);

</script>

<template>
    <component :is="layout || 'div'" :key="$route.fullPath">
        <router-view :key="$route.fullPath"/>
    </component>
</template>

<style scoped>

</style>
