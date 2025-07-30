import {
    URL_API_AUTH_LOGIN,
    URL_API_AUTH_LOGOUT,
    URL_API_AUTH_REGISTER,
    URL_API_AUTH_USER, URL_API_PASSWORD_EMAIL, URL_API_PASSWORD_RESET
} from "@/config/urls.js";
import _ from "lodash";

const mapUser = (user) => {
    if (!_.isPlainObject(user)) return user;

    user.role.isAdmin = () => user.role.name === 'super_admin';
    user.role.isSuperAdmin = () => user.role.name === 'admin';
    user.role.isUser = () => user.role.name === 'user';
    user.role.is = (roleName) => user.role.name === roleName;

    return user;
};

export async function login(credentials) {
    const response = await axios.post(URL_API_AUTH_LOGIN, credentials);

    return mapUser(response.data);
}

export async function logout() {
    const response = await axios.post(URL_API_AUTH_LOGOUT);

    return response.data;
}

export async function fetchAuthUser() {
    const response = await axios.get(URL_API_AUTH_USER);

    return mapUser(response.data);
}

export async function register(data) {
    const response = await axios.post(URL_API_AUTH_REGISTER, data);

    return response.data;
}

export async function sendPasswordResetLink(email) {
    const response = await axios.post(URL_API_PASSWORD_EMAIL, {email});

    return response.data;
}

export async function resetPassword(data) {
    const response = await axios.post(URL_API_PASSWORD_RESET, data);

    return response.data;
}
