<script setup lang="jsx">
import {useAppStore} from "@/stores/app.store.js";
import googleLogo from "@/assets/google-logo.svg"

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

const oauthProviderLogoMap = {
    google:   <img src={googleLogo} alt="Google Logo" class="tw:w-[16px] tw:h-[16px]"/>,
    facebook: <i class="pi pi-facebook tw:text-blue-500" style="font-size:16px"></i>
};

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
                class="tw:block tw:w-full tw:text-black! tw:light:bg-[white]! tw:dark:text-white! tw:hover:bg-primary-300! tw:hover:text-white!"
                variant="outlined"
                @click="() => openOAuthProviderSignInWindow(oauth_provider.url)">
                <div class="tw:flex tw:items-center tw:justify-center">
                    <component :is="oauthProviderLogoMap[oauth_provider.code]" />
                    <span class="tw:ml-2">{{ prefixText }} {{ oauth_provider.name }}</span>
                </div>
            </Button>
        </div>
    </div>
</template>

<style scoped>

</style>
