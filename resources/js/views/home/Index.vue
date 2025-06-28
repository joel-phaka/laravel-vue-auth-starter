<script setup>

import {useAuthStore} from "@/stores/auth.store.js";
import {storeToRefs} from "pinia";
import {inject} from "vue";

const authStore = useAuthStore();
const {authUser} = storeToRefs(authStore);

const {toggleThemeMode} = inject('app:theme');

const logout = async () => {
    await authStore.logoutUser();
};

const loadUser = async () => {
    await authStore.fetchAuthUser();
}
</script>

<template>
    {{ authUser?.full_name }}
    <br>
    <Button @click="logout">Logout</Button>
    <Button @click="loadUser">Load User</Button>
    <Button @click="toggleThemeMode">Change Theme</Button>
</template>

<style scoped>

</style>
