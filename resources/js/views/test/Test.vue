<script setup>

import browserStorage from "@/lib/browser-storage.js";

browserStorage.set('person', {name: 'John', contact: {email: 'john@foo.com', address: {street: 'Edward', city: 'CPT'}}});

console.log(browserStorage.get("person.name"));
</script>

<template>
    <h1>Helloo</h1>
</template>

<style scoped>

</style>
