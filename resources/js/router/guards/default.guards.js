import * as yup from "yup";
import _ from "lodash";

const defaultGuards = {
    ['verify']: {
        beforeEnter: async (to, from, next) => {
            try {
                await axios.post('http://127.0.0.1:8000/api/auth/verify/email', {
                    url: to.meta.url,
                });

                to.meta.emailVerificationStatus = 'verified';
                next();
            } catch (error) {
                const reason = error.response?.data?.reason;

                if (reason === 'email_verification_already_verified') {
                    next('/');
                } else {
                    to.meta.emailVerificationStatus = reason === 'email_verification_expired_url'
                        ? 'expired'
                        : 'failed';

                    next();
                }
            }
        }
    },
    ['reset-password']: {
        beforeEnter: async (to, from, next) => {
            const schema = yup.object({
                email: yup.string().email().required(),
            });

            if (schema.validateSync(_.pick(to.query,['email']))) {
                next();
            } else {
                next('/');
            }
        }
    }
};

export default defaultGuards;
