import {useRoute, useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/auth.store';
import {appUrl, PATH_REGEX} from "@/lib/utils.js";
import {storeToRefs} from "pinia";

export default function useAuth() {
    const router = useRouter();
    const route = useRoute();

    const authStore = useAuthStore();
    const {
        authUser,
        isLoggedIn,
        isLoggingIn,
        isLoggingOut,
        isFetchingAuthUser,
        authErrors
    } = storeToRefs(authStore)

    const login = async (credentials) => {
        return await authStore.loginUser(credentials, async () => {
            let continueToPath = '/';

            if (route.query.return_url) {
                const decodedUri = decodeURIComponent(route.query.return_url);
                const isPath = PATH_REGEX.test(decodedUri);
                const isInternalUrl = PATH_REGEX.test(decodedUri.substring(appUrl().length));

                if (isPath || isInternalUrl) {
                    const uri = "/" + (isInternalUrl ? decodedUri.substring(appUrl().length) : decodedUri)
                        .replace(/^\/+/, '')
                        .replace(/\/+$/, '')

                    if (!/^\/signin|signup/.test(uri)) continueToPath = uri;
                }
            }

            await router.push(continueToPath);
        });
    };

    const logout = async () => {
        await authStore.logoutUser(async () => await router.push({name: 'signin'}));
    };

    return {
        authUser,
        isLoggedIn,
        isLoggingIn,
        isLoggingOut,
        authErrors,
        isFetchingAuthUser,
        login,
        logout,
        fetchAuthUser: authStore.fetchAuthUser,
    }
}
