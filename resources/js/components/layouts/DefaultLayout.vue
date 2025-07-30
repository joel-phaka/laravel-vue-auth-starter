<script setup>
import {ref} from "vue";
import NavBar from "@/components/layouts/partials/NavBar.vue";
import Footer from "@/components/layouts/partials/Footer.vue";

</script>

<template>
    <NavBar/>
    <div class="app--main-container pl-3 pr-3 pb-4">
        <slot></slot>
    </div>
    <Footer/>
</template>

<style scoped>

</style>
