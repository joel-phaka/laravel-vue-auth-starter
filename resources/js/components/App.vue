<script setup>
import {shallowRef, provide, watch} from "vue";
import {useRoute} from "vue-router";
import {useRecaptchaProvider} from "vue-recaptcha";
import layouts from "@/components/layouts/index.js";
import useAuth from "@/composables/useAuth.js";

useRecaptchaProvider();

const route = useRoute();

const layout = shallowRef('div');

const {isLoggingOut} = useAuth();

const setLayout = (layoutName) => {
    layout.value = layouts[layoutName] || 'div';
};

const setPageTitle = (title) => {
    const appName = import.meta.env.VITE_APP_APP_NAME;

    if (title) document.title = `${title}${appName ? ` / ${appName}` : ''}`;
};

provide("app:setLayout", setLayout);
provide("app:setPageTitle", setPageTitle);

watch(() => route.meta.layout, setLayout, { immediate: true });
watch(() => route.meta.title, setPageTitle, { immediate: true });

</script>

<template>
    <component
        v-if="!isLoggingOut"
        :is="layout"
        :key="$route.meta.layout">
        <router-view :key="$route.fullPath"/>
    </component>
    <div
        v-else
        class="main-container tw:flex tw:md:items-center tw:md:justify-center tw:relative"
        style="min-height: 100vh">
        <div
            class="tw:fixed tw:bg-surface-200 tw:dark:bg-surface-800 center-vertical-horizontal"
            style="width: max-content; height: max-content; max-height: max-content; border-radius: 50%; aspect-ratio: 1 / 1;">
            <ProgressSpinner strokeWidth="4"/>
        </div>
    </div>
</template>

<style scoped>

</style>
