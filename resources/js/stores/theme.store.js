import {defineStore} from 'pinia';
import {ref, computed} from 'vue';
import browserStorage from '@/lib/browser-storage';

export const useThemeStore = defineStore('theme', () => {
    const themeMode = ref(['light', 'dark'].includes(browserStorage.get("themeMode")) ? browserStorage.get("themeMode") : 'light');
    const isDarkMode = ref(themeMode.value === 'dark');
    const isUpdatingThemeMode = ref(false);

    const themeColor = computed(() => isDarkMode.value ? '#000000' : '#FFFFFF');
    const themeColorInverted = computed(() => isDarkMode.value ? '#FFFFFF' : '#000000');
    const themeIcon = computed(() => isDarkMode.value ? 'pi pi-moon' : 'pi pi-sun');
    const themeIconInverted = computed(() => isDarkMode.value ? 'pi pi-sun' : 'pi pi-moon');
    const themeText = computed(() => isDarkMode.value ? 'Dark Mode' : 'Light Mode');

    const toggleThemeMode = async () => {
        if (isUpdatingThemeMode.value) return;

        isUpdatingThemeMode.value = true;

        isDarkMode.value = document.documentElement.classList.toggle('dark-mode');
        themeMode.value = isDarkMode.value ? 'dark' : 'light';

        browserStorage.set('themeMode', themeMode.value);

        isUpdatingThemeMode.value = false;
    };

    return {
        themeMode,
        isDarkMode,
        isUpdatingThemeMode,
        themeColor,
        themeColorInverted,
        themeIcon,
        themeIconInverted,
        themeText,
        toggleThemeMode
    };
});
