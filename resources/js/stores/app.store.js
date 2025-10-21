import {defineStore} from "pinia";
import {ref} from "vue";

export const useAppStore = defineStore('app', () => {
    const appSettings = ref(Object(window.__xapp__.settings));
    const appFeatures = ref(Object(window.__xapp__.features));

    return {
        appSettings,
        appFeatures,
    };
});
