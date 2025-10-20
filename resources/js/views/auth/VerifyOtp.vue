<script setup>
import {useRouter} from "vue-router";
import useAuth from "@/composables/useAuth.js";
import appLogo from "@/assets/app-logo.png";
import {inject} from "vue";
import {Checkbox as RecaptchaCheckbox} from "vue-recaptcha";
import {createFieldsSchema} from "@/lib/utils.js";
import * as yup from "yup";
import {useForm} from "vee-validate";
import {toTypedSchema} from "@vee-validate/yup";
import {useAppStore} from "@/stores/app.store.js";

const {appFeatures} = useAppStore();
const router = useRouter();

const setProcessing = inject('app:layout:auth:setProcessing');

const schema = createFieldsSchema({
        otp: yup
            .number()
            .label('OTP')
            .required()
    },
    true
);

const {meta, errors, defineField, setErrors, handleSubmit} = useForm({
    validationSchema: toTypedSchema(schema)
});

const [otp, otpAttrs] = defineField('otp');
const [recaptchaToken, recaptchaTokenAttrs] = defineField('recaptchaToken');

const {authUser} = useAuth();

const onSubmit = handleSubmit(async (values) => {
    console.log(values);
});
</script>

<template>
    <form @submit="onSubmit" class="verification-form tw:flex tw:flex-col tw:items-center">
        <div class="tw:mb-5 tw:flex tw:flex-col tw:items-center">
            <img :src="appLogo" alt="form-app-logo" class="form-app-logo">
            <h2>Verify your account</h2>
            <div class="tw:text-center tw:text-sm tw:text-gray-500">
                We've sent an OTP to {{authUser.email}}.
                Please enter the code below to confirm your identity and continue to your account.
            </div>
        </div>
        <div class="tw:mb-3 tw:flex tw:flex-col tw:items-center">
            <InputOtp v-model="otp"  size="large" :length="6"/>
            <p v-if="!!errors.otp" class="tw:mt-2 tw:text-red-500">{{ errors.otp }}</p>
        </div>
        <!--
        <div class="p-inputtext p-component p-filled tw:block tw:w-full tw:mb-3 tw:bg-slate-200 tw:dark:bg-zinc-700 tw:dark:text-zinc-400">
            {{authUser.email}}
        </div>
        -->
        <div v-if="appFeatures.recaptcha" class="tw:mt-9 tw:flex tw:justify-center">
            <div>
                <RecaptchaCheckbox v-model="recaptchaToken" v-bind="recaptchaTokenAttrs"/>
                <div v-if="!!errors.recaptchaToken" class="tw:mt-2 tw:text-red-500">
                    {{ errors.recaptchaToken }}
                </div>
            </div>
        </div>
        <div class="tw:flex tw:justify-between tw:gap-2 tw:mt-6 tw:w-[328px]">
            <Button
                type="button"
                label="Re-send"
                severity="warn"
                class=" tw:block tw:text-center">
            </Button>
            <Button
                type="submit"
                label="Verify"
                class="tw:block tw:text-center tw:flex-1">
            </Button>
        </div>
    </form>
</template>

<style scoped>

</style>
