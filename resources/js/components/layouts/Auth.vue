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
        class="main-container flex md:align-items-center md:justify-content-center relative"
        style="min-height: 100vh">
        <div v-show="!isProcessing" class="form-container pt-5 pb-7 md:px-4 mx-3 md:my-6">
            <slot class=""></slot>
        </div>
        <div v-show="isProcessing" class="fixed center-vertical-horizontal surface-ground" style="width: max-content; border-radius: 50%">
            <ProgressSpinner strokeWidth="4"/>
        </div>
        <ThemeToggleButton
            class="fixed"
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
