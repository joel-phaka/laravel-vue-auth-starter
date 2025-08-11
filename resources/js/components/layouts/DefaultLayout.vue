<script setup>
import {ref} from "vue";
import NavBar from "@/components/layouts/partials/NavBar.vue";
import Footer from "@/components/layouts/partials/Footer.vue";

</script>

<template>
    <NavBar/>
    <div class="app--main-container tw:px-4 tw:pb-6">
        <slot></slot>
    </div>
    <Footer/>
</template>

<style scoped>

</style>
