<script setup>
import {useAppStore} from "@/stores/app.store.js";
import SocialIcon from "@/components/ui/SocialIcon.vue";

const props = defineProps({
   prefixText: {
       type: String,
       default: 'Continue with',
       validator(value) {
           return !!value?.trim();
       }
   }
});

const {prefixText} = props;

const {appSettings} = useAppStore();

const openOAuthProviderSignInWindow = (url) => {
    window.location.replace(url);
};
</script>

<template>
    <div v-if="!!appSettings.oauth_providers.length">
        <div class="tw:flex tw:items-center tw:mb-3">
            <div class="tw:flex-1 tw:border border-color"></div>
            <div class="tw:flex-grow-0 tw:m-2 tw:text-gray-400">OR</div>
            <div class="tw:flex-1 tw:border border-color"></div>
        </div>
        <div class="tw:flex tw:flex-col tw:gap-2">
            <Button
                v-for="oauth_provider in appSettings.oauth_providers"
                :class="[
                    'tw:block tw:w-full',
                    'tw:justify-center!',
                    'tw:text-black!',
                    'tw:light:bg-[white]!',
                    'tw:dark:text-white!',
                    'tw:hover:bg-primary-300!',
                    'tw:hover:text-white!',
                    'tw:shadow-sm',
                    'tw:dark:bg-[#282828]!'
                ]"
                variant="outlined"
                @click="() => openOAuthProviderSignInWindow(oauth_provider.url)">
                <div class="tw:flex tw:items-center tw:justify-center">
                    <SocialIcon :provider="oauth_provider.code" :size="26"/>
                    <span class="tw:ml-2">{{ prefixText }} {{ oauth_provider.name }}</span>
                </div>
            </Button>
        </div>
    </div>
</template>

<style scoped>

</style>
