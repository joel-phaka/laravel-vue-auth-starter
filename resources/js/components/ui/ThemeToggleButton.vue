<script setup>
import {toRefs} from "vue";
import {useThemeStore} from "@/stores/theme.store.js";
import {storeToRefs} from "pinia";

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

const themeStore = useThemeStore();
const {
    isDarkMode,
    themeColor,
    themeColorInverted,
    isUpdatingThemeMode
} = storeToRefs(themeStore);
const {toggleThemeMode} = themeStore;
</script>

<template>
    <Button
        :icon="`pi pi-${isDarkMode ? 'sun' : 'moon'}`"
        rounded
        unstyled
        :disabled="isUpdatingThemeMode"
        class="tw:p-0 tw:m-0 tw:flex tw:items-center tw:justify-center tw:cursor-pointer"
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
