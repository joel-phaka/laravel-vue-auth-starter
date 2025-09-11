import * as changeCase from "change-case";
import _, {isFunction, isPlainObject} from "lodash";
import * as yup from "yup";
import {useAppStore} from "@/stores/app.store.js";

export const PATH_REGEX = /^\/(([^\/]+\/?)*|[^\/]+)(\?#.*)*$/;

function toCase(caseFunction, obj, recursive = false) {
    if (!!obj && typeof obj === 'object') {
        if (recursive) {
            if (Array.isArray(obj)) {
                return obj.map(item => {
                    return !!item && typeof item === 'object'
                        ? toCase(caseFunction, item, true)
                        : item;
                });
            } else {
                let newObj = {};

                for (const [key, value] of Object.entries(obj)) {
                    newObj[caseFunction(key)] = !!obj && typeof value === 'object'
                        ? toCase(caseFunction, value, true)
                        : value
                }

                return newObj;
            }
        } else {
            if (Array.isArray(obj)) {
                return obj.map(item => {
                    return !!item && typeof item === 'object'
                        ? toCase(caseFunction, item)
                        : item;
                });
            } else {
                let newObj = {};

                for (const [key, value] of Object.entries(obj)) {
                    newObj[caseFunction(key)] = value;
                }

                return newObj;
            }
        }
    } else {
        return obj
    }
}
export function keysToCamelCase(obj, recursive = false) {
    return toCase(changeCase.camelCase, obj, recursive);
}

export function keysToKebabCase(obj, recursive = false) {
    return toCase(changeCase.kebabCase, obj, recursive);
}

export function keysToSnakeCase(obj, recursive = false) {
    return toCase(changeCase.snakeCase, obj, recursive);
}

export function delay(milliseconds) {
    return new Promise((resolve) => {
        setTimeout(resolve, milliseconds)
    });
}

export function appUrl(path = '', encode = false) {
    let url = location.protocol + '//' + location.hostname + (!!location.port ? ':' + location.port : '');
    path = path?.trim().replace(/^\/+/, '');

    url += path ? '/' + path : '';

    return encode ? encodeURIComponent(url) : url;
}

export function normaliseError(error) {
    if (error.isNormalised) return error;

    let err = _.cloneDeep(error);

    err.response = err.response || {
        status: 500,
        data: {
            message: err.message || "Internal Server Error",
        }
    };

    err.response.hasValidationErrors = err.response?.status === 422;
    err.response.data.errors = _.isObject(err.response.data.errors)
        ? err.response.data.errors
        : {};

    err.isNormalised = true;

    return err;
}

export function createFieldsSchema(fields, useRecaptcha = false) {
    const {appFeatures} = useAppStore();

    if (useRecaptcha && appFeatures.recaptcha) {
        fields.recaptchaToken = yup
            .string()
            .required("Please complete the reCAPTCHA check.")
    }

    return yup.object(fields);
}

export function arrayOnlyIf(condition, arr) {
    if (condition && !!arr) {
        return Array.isArray(arr) ? arr : [arr];
    }

    return [];
}
