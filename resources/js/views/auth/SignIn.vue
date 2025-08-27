<script setup>
import {useForm} from "vee-validate";
import {toTypedSchema} from "@vee-validate/yup";
import * as yup from "yup";
import browserStorage from "@/lib/browser-storage.js";
import {inject, watch} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import config from "@/config";
import {keysToSnakeCase, appUrl} from "@/lib/utils.js";
import {useRoute} from "vue-router";
import appLogo from "@/assets/app-logo.png";
import useAuth from "@/composables/useAuth.js";

const route = useRoute();

const setProcessing = inject('app:layout:auth:setProcessing');
const schema = yup.object({
    email: yup
        .string()
        .label('Email')
        .required()
        .email(),
    password: yup
        .string()
        .label('Password')
        .required(),
    rememberMe: yup
        .boolean()
        .notRequired(),
    recaptchaToken: yup
        .string()
        .required("Please complete the reCAPTCHA check.")
});

const {meta, defineField, errors, handleSubmit} = useForm({
    validationSchema: toTypedSchema(schema),
    initialValues: {
        email: (browserStorage.remove('loginEmail') ?? route.query.email ?? ''),
        password: '',
        rememberMe: true,
    }
});

const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [rememberMe, rememberMeAttrs] = defineField('rememberMe');
const [recaptcha, recaptchaAttrs] = defineField('recaptchaToken');

const {isLoggingIn, authErrors, login} = useAuth();

watch(isLoggingIn, setProcessing, {immediate: true});

const onSubmit = handleSubmit(async (values) => login(keysToSnakeCase(values)));

const openExternalSignInWindow = (provider) => {
    window.location.replace(appUrl(`signin/${provider}`));
};
</script>

<template>
    <form @submit="onSubmit" class="signin-form">
        <div class="tw:mb-5">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Sign in to continue</h2>
            <p v-if="!meta.dirty">Enter your email and password to sign in.</p>
            <p v-else-if="!!authErrors?.loginError" class="tw:text-red-500">
                <template v-if="authErrors?.loginError?.data?.error_code === 'auth_invalid_credentials'">
                    Incorrect email or password
                </template>
                <template v-else>
                    An error occurred. Please try again later.
                </template>
            </p>
        </div>
        <div>
            <div class="tw:mb-3">
                <label for="email" class="tw:block tw:pb-1">Email</label>
                <InputText
                    v-model="email"
                    v-bind="emailAttrs"
                    :invalid="!!errors.email"
                    id="email"
                    name="email"
                    placeholder="Email"
                    class="tw:block tw:w-full"/>
                <p v-if="!!errors.email" class="tw:mt-2 tw:text-red-500">{{ errors.email }}</p>
            </div>
            <div>
                <label for="password" class="tw:block tw:pb-1">Password</label>
                <Password
                    v-model="password"
                    v-bind="passwordAttrs"
                    :invalid="!!errors.password"
                    inputId="password"
                    name="password"
                    placeholder="Password"
                    :feedback="false"
                    toggleMask
                    class="tw:w-full"/>
                <p v-if="!!errors.password" class="tw:mt-2 tw:text-red-500">{{ errors.password }}</p>
                <div class="tw:flex tw:align-content-center tw:mt-3 tw:text-sm">
                    <div class="tw:flex tw:items-center tw:gap-2">
                        <Checkbox
                            v-model="rememberMe"
                            v-bind="rememberMeAttrs"
                            binary
                            input-id="rememberMe"
                            name="rememberMe"/>
                        <label for="rememberMe">Remember me</label>
                    </div>
                    <router-link
                        to="/password/forgot"
                        class="no-underline tw:ml-auto default-link">
                        Forgot Password
                    </router-link>
                </div>
            </div>
            <div class="tw:my-9 tw:flex tw:justify-center">
                <div>
                    <RecaptchaCheckbox v-model="recaptcha" v-bind="recaptchaAttrs"/>
                    <div v-if="!!errors.recaptcha" class="tw:mt-2 tw:text-red-500">
                        {{ errors.recaptcha }}
                    </div>
                </div>
            </div>
            <Button type="submit" :disabled="!meta.valid" class="tw:block tw:w-full tw:text-center">Sign In</Button>
            <div class="tw:flex tw:items-center tw:my-3">
                <div class="tw:flex-1 tw:border border-color"></div>
                <div class="tw:flex-grow-0 tw:m-2 tw:text-gray-400">OR</div>
                <div class="tw:flex-1 tw:border border-color"></div>
            </div>
            <div class="tw:flex tw:flex-col tw:gap-2">
                <Button
                    class="tw:block tw:w-full"
                    variant="outlined"
                    @click="() => openExternalSignInWindow('google')">
                    <div class="tw:flex tw:items-center tw:justify-center">
                        <img src="@/assets/google-logo.svg" alt="Google Logo" class="tw:w-[16px] tw:h-[16px]">
                        <span class="tw:ml-2">Sign In with Google</span>
                    </div>
                </Button>
                <Button
                    class="tw:block tw:w-full"
                    variant="outlined"
                    @click="() => openExternalSignInWindow('facebook')">
                    <div class="tw:flex tw:items-center tw:justify-center">
                        <i class="pi pi-facebook tw:text-blue-500" style="font-size:16px"></i>
                        <span class="tw:ml-2">Sign In with Facebook</span>
                    </div>
                </Button>
            </div>
            <p v-if="config.allowSignUp" class="tw:text-center tw:mt-8 tw:mb-0">
                Don't have an account?
                <router-link to="/signup" class="no-underline default-link">Sign Up</router-link>
            </p>
        </div>
    </form>
</template>

<style scoped>

</style>
