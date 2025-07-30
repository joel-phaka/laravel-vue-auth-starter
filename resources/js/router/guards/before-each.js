import {appUrl} from "@/lib/utils.js";
import useAuth from "@/composables/useAuth.js";

export default async function beforeEach(to, from, next) {
    from.meta.url = appUrl(to.fullPath);
    to.meta.url = appUrl(to.fullPath);

    const {isLoggedIn, fetchAuthUser} = useAuth();

    if (!isLoggedIn.value && to.meta?.requireAuth) {
        await fetchAuthUser();
    }

    let navigationData = null

    if (isLoggedIn.value && to.meta?.guestOnly) {
        navigationData = {path: '/'};
    } else if (!isLoggedIn.value && to.meta?.requireAuth) {
        let query = {};

        if (!['/'].includes(to.path)) {
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
