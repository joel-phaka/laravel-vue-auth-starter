<script setup>

import {useAuthStore} from "@/stores/auth.store.js";
import {storeToRefs} from "pinia";
import {inject} from "vue";

const authStore = useAuthStore();
const {authUser} = storeToRefs(authStore);

</script>

<template>
    <div class="tw:mt-4">
        <h4>Hi {{ authUser.full_name }}!</h4>
        <p>
            {{ 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum cupiditate dolor earum perspiciatis! Accusantium consectetur corporis cum dolorem exercitationem fugiat fugit harum, impedit ipsum itaque molestias, obcaecati odio, provident quia.'.repeat(50) }}
        </p>
    </div>
</template>

<style scoped>

</style>
