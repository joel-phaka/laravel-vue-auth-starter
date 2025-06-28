import {appUrl} from "@/lib/utils.js";
import {useAuthStore} from "@/stores/auth.store.js";
import {storeToRefs} from "pinia";
import * as yup from "yup";
import _ from "lodash";

export const routeGuards = {
    ['verify']: {
        beforeEnter: async (to, from, next) => {
            try {
                await axios.post('http://127.0.0.1:8000/api/auth/verify/email', {
                    url: to.meta.url,
                });

                to.meta.emailVerificationStatus = 'verified';
                next();
            } catch (error) {
                const errorCode = error.response?.data?.error_code;

                if (errorCode === 'email_verification_already_verified') {
                    next('/');
                } else {
                    to.meta.emailVerificationStatus = errorCode === 'email_verification_expired_url'
                        ? 'expired'
                        : 'failed';

                    next();
                }
            }
        }
    },
    ['reset-password']: {
        beforeEnter: async (to, from, next) => {
            const schema = yup.object({
                email: yup.string().email().required(),
            });

            if (schema.validateSync(_.pick(to.query,['email']))) {
                next();
            } else {
                next('/');
            }
        }
    }
}

export async function beforeEach(to, from, next) {
    from.meta.url = appUrl(to.fullPath);
    to.meta.url = appUrl(to.fullPath);

    const authStore = useAuthStore();
    const {isLoggedIn, isLogoutTriggered} = storeToRefs(authStore);

    if (!isLoggedIn.value && !isLogoutTriggered.value) {
        await authStore.fetchAuthUser();
    }

    let navigationData = null

    if (isLoggedIn.value && to.meta?.guestOnly) {
        navigationData = {path: '/'};
    } else if (!isLoggedIn.value && to.meta?.requireAuth) {
        let query = {};

        if (to.path !== '/') {
            query.return_url = appUrl(to.fullPath, true);
        }

        navigationData = {
            path: 'signin',
            query
        }
    }

    if (!!navigationData) next(navigationData);
    else next();
}

export default {
    routeGuards,
    beforeEach
}
