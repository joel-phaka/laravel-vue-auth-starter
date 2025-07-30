/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import {appUrl, normaliseError} from "@/lib/utils.js";
import {useAuthStore} from "@/stores/auth.store.js";

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.interceptors.request.use(config => {
    if (typeof config.headers['Content-Type'] === 'undefined') {
        config.headers['Content-Type'] = 'application/json'
    }

    if (/^https:\/\/.+\.ngrok(?:-free\.app|\.io)(\/.*|\?.*|#.*|$)/.test(config.url)) {
        config.headers['ngrok-skip-browser-warning'] = "true";
    }

    return config;
});

window.axios.interceptors.response.use(response => response, error => Promise.reject(normaliseError(error)));

window.axios.interceptors.response.use(
    response => response,
    error => {
        const url = error.response.config?.url;
        const statusCode = error.response.status;

        if (url && (url.startsWith('/') || url.startsWith(appUrl())) && statusCode === 401) {
            const {isLoggedIn} = useAuthStore();
            const urlPath = url.replace(new RegExp(`^${appUrl()}`), '');

            if (isLoggedIn.value && !/^\/api\/((auth\/(login|register|logout))|oauth)/.test(urlPath)) {
                window.location.reload();
            }
        }

        return Promise.reject(error);
    }
);
