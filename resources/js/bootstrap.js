/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import _ from "lodash";

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.interceptors.request.use(config => {
    if (typeof config.headers['Content-Type'] === 'undefined') {
        config.headers['Content-Type'] = 'application/json'
    }

    if (/^https:\/\/.+\.ngrok-free\.app($|#.*|\?.*|\/.*)/.test(config.url)) {
        config.headers['ngrok-skip-browser-warning'] = "true";
    }

    return config;
});
window.axios.interceptors.response.use(
    response => response,
    error => {
        error.response = error.response || {
            status: 500,
            data: {
                message: error.message || "Internal Server Error",
            }
        };

        error.response.hasValidationErrors = error.response?.status === 422;
        error.response.data.errors = _.isObject(error.response.data.errors)
            ? error.response.data.errors
            : {};

        return Promise.reject(error);
    }
);
