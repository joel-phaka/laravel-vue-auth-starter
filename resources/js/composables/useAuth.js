import {useRoute, useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/auth.store';
import {toLocalUri} from "@/lib/utils.js";
import {storeToRefs} from "pinia";

export default function useAuth() {
    const router = useRouter();
    const route = useRoute();

    const authStore = useAuthStore();
    const {fetchAuthUser} = authStore;
    const {
        authUser,
        authState,
        isLoggedIn,
        isLoggingIn,
        isLoggingOut,
        isFetchingAuthUser,
        authErrors
    } = storeToRefs(authStore)

    const login = async (credentials) => {
        return await authStore.loginUser(credentials, async () => {
            let returnTo = '/';

            if (route.query.return_url) {
                const localUri = toLocalUri(route.query.return_url);

                if (!/^\/signin|signup/.test(localUri)) returnTo = localUri;
            }

            if (authState.value === 'auth_pending_login_verification') {
                const query = !returnTo.startsWith('/verify/otp') ? {return_url: returnTo} : {};
                returnTo = {name: 'verify-otp', query};
            }

            await router.replace(returnTo);
        });
    };

    const logout = async () => {
        await authStore.logoutUser(async () => await router.push({name: 'signin'}));
    };

    return {
        authUser,
        authState,
        isLoggedIn,
        isLoggingIn,
        isLoggingOut,
        authErrors,
        isFetchingAuthUser,
        login,
        logout,
        fetchAuthUser,
    }
}
