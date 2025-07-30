import {defineStore} from "pinia";
import * as authService from "@/services/auth.service.js";
import _ from "lodash";
import {normaliseError} from "@/lib/utils.js";

const getDefaultState = () => {
    return {
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
        async loginUser(credentials, callback = null) {
            try {
                this.clearErrors()
                this.isLoggingIn = true;

                this.authUser = await authService.login(credentials);

                if (_.isFunction(callback)) await callback();
            } catch (error) {
                this.authErrors.loginError = normaliseError(error);
                console.log(error);
            } finally {
                this.isLoggingIn = false;
            }

            return this.authUser;
        },
        async logoutUser(callback) {
            try {
                this.clearErrors()
                this.isLoggingOut = true;

                await authService.logout();
            } catch (error) {
                this.authErrors.logoutError = normaliseError(error);
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
                this.authErrors.fetchUserError = normaliseError(error);
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
