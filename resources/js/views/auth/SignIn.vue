<script setup>
import {useForm} from "vee-validate";
import {toTypedSchema} from "@vee-validate/yup";
import * as yup from "yup";
import {computed, inject, watch} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import {keysToSnakeCase, createFieldsSchema} from "@/lib/utils.js";
import {useRoute} from "vue-router";
import appLogo from "@/assets/app-logo.png";
import useAuth from "@/composables/useAuth.js";
import {useAppStore} from "@/stores/app.store.js"
import OAuthProviderLinks from "@/components/auth/OAuthProviderLinks.vue";

const {appFeatures} = useAppStore();

const route = useRoute();

const setProcessing = inject('app:layout:auth:setProcessing');

const schema = createFieldsSchema({
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
    },
    true
);

const {meta, errors, defineField, setErrors, handleSubmit} = useForm({
    validationSchema: toTypedSchema(schema),
    initialValues: {
        email: '',
        password: '',
        rememberMe: true,
    }
});

const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [rememberMe, rememberMeAttrs] = defineField('rememberMe');
const [recaptchaToken, recaptchaTokenAttrs] = defineField('recaptchaToken');

const {isLoggingIn, authErrors, login} = useAuth();
const loginError = computed(() => authErrors.value?.loginError);

watch(isLoggingIn, setProcessing, {immediate: true});
watch(loginError,  (newValue) => {
    if (newValue?.hasValidationErrors) setErrors(newValue.validationErrors);
});

const onSubmit = handleSubmit(async (values) => await login(keysToSnakeCase(values)));
</script>

<template>
    <form @submit="onSubmit" class="signin-form">
        <div class="tw:mb-5">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Sign in to continue</h2>
            <p v-if="!meta.dirty">Enter your email and password to sign in.</p>
            <p v-else-if="!!loginError" class="tw:text-red-500">
                <template v-if="loginError?.response?.data?.reason === 'auth_invalid_credentials'">
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
                        v-if="appFeatures.password_reset"
                        to="/password/forgot"
                        class="no-underline tw:ml-auto default-link">
                        Forgot Password
                    </router-link>
                </div>
            </div>
            <div v-if="appFeatures.recaptcha" class="tw:mt-9 tw:flex tw:justify-center">
                <div>
                    <RecaptchaCheckbox v-model="recaptchaToken" v-bind="recaptchaTokenAttrs"/>
                    <div v-if="!!errors.recaptchaToken" class="tw:mt-2 tw:text-red-500">
                        {{ errors.recaptchaToken }}
                    </div>
                </div>
            </div>
            <Button
                type="submit"
                label="Sign In"
                :disabled="!meta.valid"
                class="tw:mt-9 tw:block tw:w-full tw:text-center">
            </Button>
            <OAuthProviderLinks class="tw:mt-3"/>
            <p v-if="appFeatures.user_registration" class="tw:text-center tw:mt-8 tw:mb-0">
                Don't have an account?
                <router-link to="/signup" class="no-underline default-link">Sign Up</router-link>
            </p>
        </div>
    </form>
</template>

<style scoped>

</style>
