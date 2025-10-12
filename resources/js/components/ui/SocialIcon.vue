<script setup lang="jsx">
import {toRefs, computed} from "vue";
import googleLogo from "@/assets/social-icons/google-logo.svg"
import facebookLogo from "@/assets/social-icons/facebook-logo.svg"
import microsoftLogo from "@/assets/social-icons/microsoft-logo.svg"
import appleLogo from "@/assets/social-icons/apple-logo.svg"

const props = defineProps({
    provider: {
        type: String,
        required: true,
        validator: (value) => [
            'google',
            'facebook',
            'apple',
            'microsoft',
        ].includes(value)
    },
    size: {
        type: Number,
        required: false,
        default: 24,
        validator: (value) => value > 0
    }
});

const {provider, size} = toRefs(props);

const socialIconMap = {
    google:    <img src={googleLogo} alt="Google Logo" class={`tw:w-[${size.value}px] tw:h-[${size.value}px]`}/>,
    facebook:  <img src={facebookLogo} alt="Facebook Logo" class={`tw:w-[${size.value}px] tw:h-[${size.value}px]`}/>,
    microsoft: <img src={microsoftLogo} alt="Microsoft Logo" class={`tw:w-[${size.value}px] tw:h-[${size.value}px]`}/>,
    apple:     <img src={appleLogo} alt="Apple Logo" class={`tw:w-[${size.value}px] tw:h-[${size.value}px]`}/>,
};

const socialIcon = computed(() => socialIconMap[provider.value]);
</script>

<template>
    <component :is="socialIcon" />
</template>

<style scoped>

</style>
