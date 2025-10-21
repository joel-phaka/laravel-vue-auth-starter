/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import {normaliseError, toLocalUri} from "@/lib/utils.js";
import {useAuthStore} from "@/stores/auth.store.js";
import router from "@/router/index.js";

window.axios = axios;
window.axios.interceptors.request.use(config => {
    const isLocalUrl = !!toLocalUri(config.url);

    if (!isLocalUrl) return config;

    config.headers['X-Requested-With'] = 'XMLHttpRequest';
    config.withCredentials = true;

    if (typeof config.headers['Content-Type'] === 'undefined') {
        config.headers['Content-Type'] = 'application/json'
    }

    if (/^https:\/\/.+\.ngrok(?:-free\.app|\.io)(\/.*|\?.*|#.*|$)/.test(config.url)) {
        config.headers['ngrok-skip-browser-warning'] = "true";
    }

    return config;
});

window.axios.interceptors.response.use(
    response => response,
    error => Promise.reject(normaliseError(error))
);

window.axios.interceptors.response.use(
    response => response,
    error => {
        const url = error.response?.config?.url;
        const localUri = toLocalUri(url);

        if (!localUri) return Promise.reject(error);

        const statusCode = error.response?.status;
        const reason = error.response?.data?.reason;

        const {isLoggedIn} = useAuthStore();

        if (statusCode === 401) {
            if (isLoggedIn.value && !/^\/api\/((auth\/(login|register|logout))|oauth)/.test(localUri)) {
                window.location.reload();
            }
        }

        return Promise.reject(error);
    }
);
