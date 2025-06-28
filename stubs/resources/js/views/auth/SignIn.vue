<script setup>
import {useForm} from "vee-validate";
import {toTypedSchema} from "@vee-validate/yup";
import * as yup from "yup";
import {useAuthStore} from "@/stores/auth.store.js";
import browserStorage from "@/lib/browser-storage.js";
import {storeToRefs} from "pinia";
import {inject, watch} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import config from "@/config";
import {keysToSnakeCase, appUrl} from "@/lib/utils.js";
import {useRoute} from "vue-router";
import appLogo from "@/assets/app-logo.png";

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

const authStore = useAuthStore();
const {isLoggingIn, authError} = storeToRefs(authStore);
const {loginUser} = authStore;

watch(isLoggingIn, setProcessing, {immediate: true, deep: true});

const onSubmit = handleSubmit(async (values) => {
    const credentials = keysToSnakeCase(values);

    await loginUser(credentials);
});

const openExternalSignInWindow = (provider) => {
    window.location.replace(appUrl(`signin/${provider}`));
};
</script>

<template>
    <form @submit="onSubmit" class="signin-form">
        <div class="mb-5">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Sign in to continue</h2>
            <p v-if="!meta.dirty">Enter your email and password to sign in.</p>
            <p v-else-if="!!authError?.loginError" class="text-red-500">
                <template v-if="authError?.loginError?.data?.error_code === 'auth_invalid_credentials'">
                    Incorrect email or password
                </template>
                <template v-else>
                    An error occurred. Please try again later.
                </template>
            </p>
        </div>
        <div>
            <div class="mb-3">
                <label for="email" class="block pb-1">Email</label>
                <InputText
                    v-model="email"
                    v-bind="emailAttrs"
                    :invalid="!!errors.email"
                    id="email"
                    name="email"
                    placeholder="Email"
                    class="block w-full"/>
                <p v-if="!!errors.email" class="mt-2 text-red-500">{{ errors.email }}</p>
            </div>
            <div class="mb-6">
                <label for="password" class="block pb-1">Password</label>
                <Password
                    v-model="password"
                    v-bind="passwordAttrs"
                    :invalid="!!errors.password"
                    inputId="password"
                    name="password"
                    placeholder="Password"
                    :feedback="false"
                    toggleMask
                    class="w-full"/>
                <p v-if="!!errors.password" class="mt-2 text-red-500">{{ errors.password }}</p>
                <div class="flex align-content-center mt-3 font-size-small">
                    <div class="flex items-center gap-2 align-items-center">
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
                        class="no-underline ml-auto">
                        Forgot Password
                    </router-link>
                </div>
            </div>
            <div class="mb-6 flex justify-content-center">
                <div>
                    <RecaptchaCheckbox v-model="recaptcha" v-bind="recaptchaAttrs"/>
                    <div v-if="!!errors.recaptcha" class="mt-2 text-red-500">
                        {{ errors.recaptcha }}
                    </div>
                </div>
            </div>
            <Button type="submit" :disabled="!meta.valid" class="block w-full mt-5 text-center">Sign In</Button>
            <div class="flex align-items-center my-3">
                <div class="flex-1 border-1 border-color"></div>
                <div class="flex-grow-0 m-2 text-gray-400">OR</div>
                <div class="flex-1 border-1 border-color"></div>
            </div>
            <Button
                label="Sign In with Google"
                class="block w-full text-center"
                severity="info" variant="outlined"
                @click="() => openExternalSignInWindow('google')"/>
            <p v-if="config.allowSignUp" class="text-center mt-5 mb-0">
                Don't have an account?
                <router-link to="/signup" class="no-underline">Sign Up</router-link>
            </p>
        </div>
    </form>
</template>

<style scoped>

</style>
