export const withApiUrl                                = (path= '') => `/api${!!path ? `/${path}` : null}`;

export const URL_API_AUTH_LOGIN                 = withApiUrl('auth/login');
export const URL_API_AUTH_REGISTER              = withApiUrl('auth/register');
export const URL_API_AUTH_LOGOUT                = withApiUrl('auth/logout');
export const URL_API_AUTH_USER                  = withApiUrl('auth/user');
export const URL_API_PASSWORD_EMAIL             = withApiUrl('auth/password/email');
export const URL_API_PASSWORD_RESET             = withApiUrl('auth/password/reset');
