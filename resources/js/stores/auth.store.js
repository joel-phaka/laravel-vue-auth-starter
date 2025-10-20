import {defineStore} from "pinia";
import * as authService from "@/services/auth.service.js";
import _ from "lodash";
import {normaliseError} from "@/lib/utils.js";

const getDefaultState = () => {
    return {
        authState: null,
        authUser: null,
        isLoggingIn: false,
        isLoggingOut: false,
        isFetchingAuthUser: false,
        authErrors: {
            loginError: null,
            logoutError: null,
            fetchAuthUserError: null,
        }
    };
};

export const useAuthStore = defineStore('auth', {
    state: () => ({...getDefaultState()}),
    getters: {
        isLoggedIn: state => !!state.authUser?.id && !!state.authUser.email,
    },
    actions: {
        async setAuthState(authState) {
            this.authState = authState;
        },
        async loginUser(credentials, callback = null) {
            try {
                this.clearErrors()
                this.isLoggingIn = true;

                const {user, auth_state} = await authService.login(credentials);
                this.authUser = user;
                this.authState = auth_state;

                if (_.isFunction(callback)) await callback({authUser: user, authState: auth_state});
            } catch (error) {
                this.authErrors.loginError = error;

                console.log(error);
            } finally {
                this.isLoggingIn = false;
            }
        },
        async logoutUser(callback = null) {
            try {
                this.clearErrors()
                this.isLoggingOut = true;

                await authService.logout();
            } catch (error) {
                this.authErrors.logoutError = error;
                console.log(error);
            } finally {
                this.authUser = null;

                if (_.isFunction(callback)) await callback();
                else window.location.reload();

                this.isLoggingOut = false;
            }
        },
        async fetchAuthUser() {
            try {
                this.clearErrors();

                this.isFetchingAuthUser = true;

                this.authUser = await authService.fetchAuthUser();
            } catch (error) {
                this.authErrors.fetchUserError = error;
                console.log(error);
            } finally {
                this.isFetchingAuthUser = false;
            }

            return this.authUser;
        },
        clearErrors() {
            this.authErrors.loginError = null;
            this.authErrors.logoutError = null;
            this.authErrors.fetchUserError = null;
        }
    }
});
