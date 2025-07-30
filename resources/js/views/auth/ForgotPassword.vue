<script setup>
import {useForm} from "vee-validate";
import { toTypedSchema } from "@vee-validate/yup";
import * as yup from "yup";
import {inject, ref} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import * as authService from "@/services/auth.service.js";
import {useRouter} from "vue-router";

const router = useRouter();

const setProcessing = inject('app:layout:auth:setProcessing');

const schema = yup.object({
    email: yup
        .string()
        .label("Email")
        .required()
        .email(),
    recaptchaToken: yup
        .string()
        .required("Please complete the reCAPTCHA check.")
});

const { meta, defineField, errors, handleSubmit } = useForm({
    validationSchema: toTypedSchema(schema),
});

const [email, emailAttrs] = defineField('email');
const [recaptchaToken, recaptchaTokenAttrs] = defineField('recaptchaToken');

const forgotPasswordError = ref();
const isEmailSent = ref(false);

const onSubmit = handleSubmit(async (values) => {
    try {
        setProcessing(true);
        forgotPasswordError.value = null;

        await authService.sendPasswordResetLink(values.email);

        isEmailSent.value = true;
    } catch (error) {
        forgotPasswordError.value = error;
    } finally {
        setProcessing(false);
    }
});
</script>

<template>
    <form @submit="onSubmit" class="forgot-password-form">
        <div class="mb-5">
            <!--<img :src="siteLogo" alt="form-site-logo" class="form-site-logo">-->
            <h2>Forgot Your Password?</h2>
            <p>
                Reset your password by submitting your email address linked to your account.
                We will send you an email with a link to choose a new password.
            </p>
        </div>
        <div>
            <Message
                v-if="forgotPasswordError?.response?.hasValidationErrors && forgotPasswordError?.response?.data?.errors?.email[0]"
                severity="error"
                class="mb-5">
                {{ forgotPasswordError?.response?.data?.errors?.email[0] }}
            </Message>
            <div>
                <div class="mb-3">
                    <InputText
                        v-model="email"
                        v-bind="emailAttrs"
                        :invalid="!!errors.email"
                        :disabled="isEmailSent"
                        id="email"
                        placeholder="Email"
                        class="block w-full"/>
                    <p v-if="!!errors.email" class="mt-2 text-red-500">{{errors.email}}</p>
                </div>
            </div>
            <div v-if="!isEmailSent">
                <div class="mb-3 flex justify-content-center">
                    <div>
                        <RecaptchaCheckbox v-model="recaptchaToken" v-bind="recaptchaTokenAttrs"/>
                        <div v-if="!!errors.recaptchaToken" class="mt-2 text-red-500">
                            {{ errors.recaptchaToken }}
                        </div>
                    </div>
                </div>
                <Button
                    v-if="!isEmailSent"
                    type="submit"
                    :disabled="!meta.valid"
                    class="block w-full mt-5 text-center">
                    Submit
                </Button>
                <p class="text-center mt-5 mb-0">
                    Do you remember your password? <router-link to="/signin" class="no-underline">Sign In</router-link>
                </p>
            </div>
        </div>
        <Dialog
            :visible="isEmailSent"
            :modal="true"
            :closable="false"
            :closeOnEscape="false"
            :draggable="false"
            :breakpoints="{ '365px': '90vw' }"
            pt:mask:class="backdrop-blur-sm">
            <p>We have emailed your password reset link.</p>
            <template #footer>
                <Button
                    label="OK"
                    outlined
                    severity="secondary"
                    autofocus
                    @click="() => {
                        router.replace({path: '/signin'});
                    }"/>
            </template>
        </Dialog>
    </form>
</template>

<style scoped>

</style>
