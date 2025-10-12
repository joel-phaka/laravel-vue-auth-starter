<script setup>
import {useForm} from "vee-validate";
import { toTypedSchema } from "@vee-validate/yup";
import * as yup from "yup";
import {inject, ref} from "vue";
import appLogo from "@/assets/app-logo.png"
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import * as authService from "@/services/auth.service.js";
import {useRoute, useRouter} from "vue-router";
import {keysToSnakeCase, createFieldsSchema} from "@/lib/utils.js";
import {useAppStore} from "@/stores/app.store.js"

const {appFeatures} = useAppStore();

const props = defineProps(['token']);

const router = useRouter();
const route = useRoute();

const {token} = props;
const email = route.query.email;

const setProcessing = inject('app:layout:auth:setProcessing');

const schema = createFieldsSchema({
        password: yup
            .string()
            .label("Password")
            .required(),
        passwordConfirmation: yup
            .string()
            .label("Confirm Password")
            .required()
            .oneOf([yup.ref("password")], 'Passwords do not match')
            .required(),
    },
    true
);

const { meta, defineField, errors, handleSubmit } = useForm({
    validationSchema: toTypedSchema(schema),
});

const [password, passwordAttrs] = defineField('password');
const [passwordConfirmation, passwordConfirmationAttrs] = defineField('passwordConfirmation');
const [recaptchaToken, recaptchaTokenAttrs] = defineField('recaptchaToken');

const resetPasswordError = ref();
const isPasswordReset = ref(false);

const onSubmit = handleSubmit(async (values) => {
    try {
        setProcessing(true);
        resetPasswordError.value = null;

        const passwordResetData = {
            token: token,
            email: email,
            ...keysToSnakeCase(values)
        };

        await authService.resetPassword(passwordResetData);

        isPasswordReset.value = true;
    } catch (error) {
        resetPasswordError.value = error;
    } finally {
        setProcessing(false);
    }
});
</script>

<template>
    <form @submit="onSubmit" class="forgot-password-form">
        <div class="tw:mb-8">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Reset Your Password</h2>
            <p>Enter your new password below to regain access to your account.</p>
        </div>
        <div>
            <Message
                v-if="resetPasswordError?.response?.hasValidationErrors && resetPasswordError?.response?.data?.errors?.email[0]"
                severity="error"
                class="tw:mb-8">
                {{ resetPasswordError?.response?.data?.errors?.email[0] }}
            </Message>
            <div>
                <div class="tw:mb-3">
                    <label for="password" class="tw:block tw:pb-1">New Password</label>
                    <Password
                        v-model="password"
                        v-bind="passwordAttrs"
                        :invalid="!!errors.password"
                        :disabled="isPasswordReset"
                        inputId="password"
                        placeholder="Password"
                        :feedback="false"
                        toggleMask
                        class="tw:w-full"/>
                    <p v-if="!!errors.password" class="tw:mt-2 tw:text-red-500">{{errors.password}}</p>
                </div>
                <div class="tw:mb-8">
                    <label for="passwordConfirmation" class="tw:block tw:pb-1">Confirm Password</label>
                    <Password
                        v-model="passwordConfirmation"
                        v-bind="passwordConfirmationAttrs"
                        :invalid="!!errors.passwordConfirmation"
                        :disabled="isPasswordReset"
                        inputId="passwordConfirmation"
                        placeholder="Confirm Password"
                        :feedback="false"
                        toggleMask
                        class="tw:w-full"/>
                    <p v-if="!!errors.passwordConfirmation" class="tw:mt-2 tw:text-red-500">{{errors.passwordConfirmation}}</p>
                </div>
            </div>
            <div v-if="!isPasswordReset">
                <div v-if="appFeatures.recaptcha" class="tw:flex tw:justify-center">
                    <div>
                        <RecaptchaCheckbox v-model="recaptchaToken" v-bind="recaptchaTokenAttrs"/>
                        <div v-if="!!errors.recaptchaToken" class="tw:mt-2 tw:text-red-500">
                            {{ errors.recaptchaToken }}
                        </div>
                    </div>
                </div>
                <Button
                    v-if="!isPasswordReset"
                    type="submit"
                    :disabled="!meta.valid"
                    class="tw:block tw:w-full tw:my-8 tw:text-center">
                    Reset Password
                </Button>
                <p class="tw:text-center tw:mb-0">
                    Do you remember your password? <router-link to="/signin" class="no-underline default-link">Sign In</router-link>
                </p>
            </div>
        </div>
        <Dialog
            :visible="isPasswordReset"
            :modal="true"
            :closable="false"
            :closeOnEscape="false"
            :draggable="false"
            :breakpoints="{ '365px': '90vw' }"
            pt:mask:class="backdrop-blur-sm">
            <p>Your password has been reset. You may proceed to sign in.</p>
            <template #footer>
                <Button
                    label="Sign In"
                    outlined
                    severity="secondary"
                    autofocus
                    @click="() => {
                        router.replace({
                            path: '/signin',
                            query: { email }
                        });
                    }"/>
            </template>
        </Dialog>
    </form>
</template>

<style scoped>

</style>
