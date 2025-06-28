<script setup>
import {inject, toRefs} from "vue";

const props = defineProps({
    size: {
        type: Number,
        default: 32,
        validator: (value) => value > 0
    },
    rounded: {
        type: Boolean,
        default: true
    }
});

const {size, rounded} = toRefs(props);

const {
    toggleThemeMode,
    isDarkMode,
    themeColor,
    themeColorInverted,
    isUpdatingThemeMode
} = inject('app:theme');
</script>

<template>
    <Button
        :icon="`pi pi-${isDarkMode ? 'sun' : 'moon'}`"
        rounded
        unstyled
        :disabled="isUpdatingThemeMode"
        class="p-0 m-0 flex align-items-center justify-content-center cursor-pointer"
        :style="{'width': `${size}px`, 'height': `${size}px`, 'border-radius': (rounded ? '50%' : '6px') }"
        :pt:label:style="{display: 'none'}"
        @click="toggleThemeMode"/>
</template>

<style scoped>
button {
    border: none;
    background: v-bind(themeColorInverted);
    color: v-bind(themeColor);
}
</style>
