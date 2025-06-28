import {defineStore} from "pinia";
import router from "@/router/index.js";
import * as authService from "@/services/auth.service.js";
import {PATH_REGEX, appUrl} from "@/lib/utils.js";

const getDefaultState = () => {
    return {
        authUser: null,
        isLoggingIn: false,
        isLoggingOut: false,
        isLogoutTriggered: false,
        isFetchingAuthUser: false,
        checkAuth: false,
        authError: {
            loginError: null,
            fetchUserError: null,
        }
    };
};

export const useAuthStore = defineStore('auth', {
    state: () => ({...getDefaultState()}),
    getters: {
        isLoggedIn: state => !!state.authUser,
    },
    actions: {
        async loginUser(credentials) {
            try {
                this.clearErrors()

                this.isLoggingIn = true;
                this.isLogoutTriggered = false;

                this.authUser = await authService.login(credentials);

                let continueToPath = '/';
                const currentRoute = router.currentRoute.value;

                if (currentRoute?.query.return_url) {
                    const decodedUri = decodeURIComponent(currentRoute.query.return_url);
                    const isPath = PATH_REGEX.test(decodedUri);
                    const isInternalUrl = PATH_REGEX.test(decodedUri.substring(appUrl().length));

                    if (isPath || isInternalUrl) {
                        const uri = "/" + (isInternalUrl ? decodedUri.substring(appUrl().length) : decodedUri)
                            .replace(/^\/+/, '')
                            .replace(/\/+$/, '')

                        if (!/^\/signin|signup/.test(uri)) {
                            continueToPath = uri;
                        }
                    }
                }

                await router.replace(continueToPath);
            } catch (error) {
                this.authError.loginError = error.response ?? error.message;
            } finally {
                this.isLoggingIn = false;
            }

            return this.authUser;
        },
        async logoutUser() {
            try {
                this.clearErrors()

                this.isLoggingOut = true;

                await authService.logout();
            } catch (error) {
                //
            } finally {
                this.authUser = null;
                this.isLoggingOut = false;
                this.isLogoutTriggered = true;
            }
        },
        async fetchAuthUser() {
            try {
                this.clearErrors();

                this.isFetchingAuthUser = true;

                this.authUser = await authService.fetchAuthUser();
            } catch (error) {
                this.authError.fetchUserError = error.response ?? error.message;
            } finally {
                this.isFetchingAuthUser = false;
            }

            return this.authUser;
        },
        clearErrors() {
            this.authError.loginError = null;
            this.authError.fetchUserError = null;
        }
    }
});
