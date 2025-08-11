<script setup>
import {ref, provide} from "vue";
import ThemeToggleButton from "@/components/ui/ThemeToggleButton.vue";

const isProcessing = ref(false);

provide('app:layout:auth:isProcessing', isProcessing);
provide('app:layout:auth:setProcessing', (v) => {
    isProcessing.value = v;
});
</script>

<template>
    <div
        class="main-container tw:flex tw:md:items-center tw:md:justify-center tw:relative"
        style="min-height: 100vh">
        <div v-show="!isProcessing" class="form-container tw:pt-8 tw:pb-16 tw:md:px-6 tw:mx-6 tw:md:my-12">
            <slot class=""></slot>
        </div>
        <div
            v-show="isProcessing"
            class="tw:fixed tw:bg-surface-200 tw:dark:bg-surface-800 center-vertical-horizontal"
            style="width: max-content; height: max-content; max-height: max-content; border-radius: 50%; aspect-ratio: 1 / 1;">
            <ProgressSpinner strokeWidth="4"/>
        </div>
        <ThemeToggleButton
            class="tw:fixed"
            style="top: 20px; right: 20px;"/>
    </div>
</template>

<style scoped>
.form-container {
    width: 100%;
    border-radius: 8px;
    height: max-content;
}
@media screen and (min-width: 768px) {
    .form-container {
        background-color: var(--x-container-bg-color);
    }
    .form-container:has(> .signin-form) {
        width: 450px;
    }
    .form-container:has(> :is(.signup-form, .forgot-password-form, .password-reset-form)) {
        width: 600px;
    }
}
</style>
