<script setup>
import {useForm} from "vee-validate";
import { toTypedSchema } from "@vee-validate/yup";
import * as yup from "yup";
import browserStorage from "@/lib/browser-storage.js";
import {inject, ref} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import {keysToSnakeCase} from "@/lib/utils.js";
import * as authService from "@/services/auth.service.js";
import {useRouter} from "vue-router";
import appLogo from "@/assets/app-logo.png";

const router = useRouter();

const setProcessing = inject('app:layout:auth:setProcessing');

const schema = yup.object({
    firstName: yup
        .string()
        .label("First Name")
        .required(),
    lastName: yup
        .string()
        .label("Last Name")
        .required(),
    email: yup
        .string()
        .label("Email")
        .required()
        .email(),
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
    acceptTerms: yup
        .bool()
        .oneOf([true], "Please read the terms and conditions and then accept them to continue."),
    recaptchaToken: yup
        .string()
        .required("Please complete the reCAPTCHA check.")
});

const { meta, defineField, errors, handleSubmit } = useForm({
    validationSchema: toTypedSchema(schema),
});

const [firstName, firstNameAttrs] = defineField('firstName');
const [lastName, lastNameAttrs] = defineField('lastName');
const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [passwordConfirmation, passwordConfirmationAttrs] = defineField('passwordConfirmation');
const [acceptTerms, acceptTermsAttrs] = defineField('acceptTerms');
const [recaptchaToken, recaptchaTokenAttrs] = defineField('recaptchaToken');

const registrationError = ref();

const onSubmit = handleSubmit(async (values) => {
    try {
        setProcessing(true);
        registrationError.value = null;

        const userData = keysToSnakeCase(values);

        await authService.register(userData);

        browserStorage.set('loginEmail', email.value);

        await router.replace({
            path: '/signin',
        });
    } catch (error) {
        registrationError.value = error;
        browserStorage.remove('loginEmail');
    } finally {
        setProcessing(false);
    }
});
</script>

<template>
    <form @submit="onSubmit" class="signup-form">
        <div class="mb-5">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Create a new account</h2>
            <p v-if="!meta.dirty">Enter your details to create a new account.</p>
            <template v-else-if="registrationError">
                An error occurred. Please try again later.
            </template>
        </div>
        <div>
            <div class="mb-3">
                <label for="firstName" class="block pb-1">First Name</label>
                <InputText
                    v-model="firstName"
                    v-bind="firstNameAttrs"
                    :invalid="!!errors.firstName"
                    id="firstName"
                    name="firstName"
                    placeholder="First Name"
                    class="block w-full"/>
                <p v-if="!!errors.firstName" class="mt-2 text-red-500">{{errors.firstName}}</p>
            </div>
            <div class="mb-3">
                <label for="lastName" class="block pb-1">Last Name</label>
                <InputText
                    v-model="lastName"
                    v-bind="lastNameAttrs"
                    :invalid="!!errors.lastName"
                    id="lastName"
                    name="lastName"
                    placeholder="Last Name"
                    class="block w-full"/>
                <p v-if="!!errors.lastName" class="mt-2 text-red-500">{{errors.lastName}}</p>
            </div>
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
                <p v-if="!!errors.email" class="mt-2 text-red-500">{{errors.email}}</p>
            </div>
            <div class="mb-3">
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
                <p v-if="!!errors.password" class="mt-2 text-red-500">{{errors.password}}</p>
            </div>
            <div class="mb-5">
                <label for="passwordConfirmation" class="block pb-1">Confirm Password</label>
                <Password
                    v-model="passwordConfirmation"
                    v-bind="passwordConfirmationAttrs"
                    :invalid="!!errors.passwordConfirmation"
                    inputId="passwordConfirmation"
                    name="passwordConfirmation"
                    placeholder="Confirm Password"
                    :feedback="false"
                    toggleMask
                    class="w-full"/>
                <p v-if="!!errors.passwordConfirmation" class="mt-2 text-red-500">{{errors.passwordConfirmation}}</p>
            </div>
            <div class="mb-6">
                <div class="mb-3 flex align-items-center">
                    <Checkbox
                        v-model="acceptTerms"
                        v-bind="acceptTermsAttrs"
                        binary
                        input-id="acceptTerms"
                        name="acceptTerms"/>
                    <label for="acceptTerms" class="ml-2 cursor-pointer">I accept terms and conditions</label>
                </div>
                <p v-if="!!errors.acceptTerms" class="mt-2 text-red-500">{{errors.acceptTerms}}</p>
            </div>
            <div class="mb-6 flex justify-content-center">
                <div>
                    <RecaptchaCheckbox v-model="recaptchaToken" v-bind="recaptchaTokenAttrs"/>
                    <div v-if="!!errors.recaptchaToken" class="mt-2 text-red-500">
                        {{ errors.recaptchaToken }}
                    </div>
                </div>
            </div>
            <Button type="submit" :disabled="!meta.valid" class="block w-full mt-5 text-center">Sign Up</Button>
            <p class="text-center mt-5 mb-0">
                Already have an account? <router-link to="/signin" class="no-underline">Sign In</router-link>
            </p>
        </div>
    </form>
</template>

<style scoped>

</style>
