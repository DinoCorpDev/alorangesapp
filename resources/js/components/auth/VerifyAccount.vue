<template>
    <v-dialog content-class="modal-register" v-model="showVerifyAccount">
        
        <v-card class="modal-register-card">
            <v-card-title style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="justify-center text-xs-left justify-left title mb-2">
                Código de verificación
                <button @click="closeVerify" class="esc-button">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.15128 1.15128C1.61991 0.682647 2.3797 0.682647 2.84833 1.15128L7.9998 6.30275L13.1513 1.15128C13.6199 0.682647 14.3797 0.682647 14.8483 1.15128C15.317 1.61991 15.317 2.3797 14.8483 2.84833L9.69686 7.9998L14.8483 13.1513C15.317 13.6199 15.317 14.3797 14.8483 14.8483C14.3797 15.317 13.6199 15.317 13.1513 14.8483L7.9998 9.69686L2.84833 14.8483C2.3797 15.317 1.61991 15.317 1.15128 14.8483C0.682647 14.3797 0.682647 13.6199 1.15128 13.1513L6.30275 7.9998L1.15128 2.84833C0.682647 2.3797 0.682647 1.61991 1.15128 1.15128Z" fill="white"/>
                    </svg>
                </button>
            </v-card-title>


            <div class="pa-5 border overflow-hidden shadow-light">
                <p>
                    Digita tu correo electrónico y el código de verificación que te hemos enviado para poder verficar la cuenta.
                </p>
                
                <v-form ref="loginForm" lazy-validation @submit.prevent="verifyAccount()">
                    <div
                        v-if="
                            authSettings.customer_login_with == 'email' ||
                            (authSettings.customer_login_with == 'email_phone' &&
                                authSettings.customer_otp_with == 'email')
                        "
                        class="mb-4"
                    >
                        <div class="mb-1 fs-13 fw-500">Correo electrónico</div>
                        <CustomInput
                            v-model="form.email"
                            :placeholder="'Correo electrónico'"
                            type="email"
                            block
                            :error-messages="emailErrors"
                            hide-details="auto"
                            required
                            outlined
                        ></CustomInput>
                    </div>
                    <div
                        v-if="
                            authSettings.customer_login_with == 'phone' ||
                            (authSettings.customer_login_with == 'email_phone' &&
                                authSettings.customer_otp_with == 'phone')
                        "
                        class="mb-4"
                    >
                        <div class="mb-1 fs-13 fw-500">
                            {{ $t("phone_number") }}
                        </div>
                        <CustomInput
                            v-model="form.phone"
                            v-bind="mobileInputProps"
                            :only-countries="availableCountries"
                            @validate="phoneValidate"
                        >
                            <template #arrow-icon><span class="vti__dropdown-arrow">&nbsp;▼</span></template>
                        </CustomInput>
                        <div v-if="$v.form.phone.$error" class="v-text-field__details mt-2 pl-3">
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message">{{ $t("Este campo es requerido") }}</div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="!$v.form.phone.$error && form.showInvalidPhone"
                            class="v-text-field__details mt-2 pl-3"
                        >
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message">
                                        {{ $t("phone_number_must_be_valid") }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="mb-1 fs-13 fw-500">Código</div>
                        <CustomInput
                            v-model="form.code"
                            length="6"
                            type="number"
                            :error-messages="codeErrors"
                            hide-details="auto"
                            :disabled="loading"
                            placeholder="Código"
                            block
                            required
                        ></CustomInput>
                    </div>
                </v-form>
            </div>

            <v-card-actions class="d-block">
                <div class="d-flex align-center justify-space-between" style="gap: 5rem;">
                    
                    <CustomButton
                        block
                        class="mb-2"
                        type="button"
                        color="orange2"
                        :loading="resendLoading"
                        :disabled="resendLoading"
                        @click.stop="resendCode"
                        >Reenviar código</CustomButton
                    >
                    <CustomButton
                        block
                        class="mb-3"
                        type="submit"
                        color="orange"
                        :loading="loading"
                        :disabled="loading"
                        @click.stop="verifyAccount"
                        >Enviar</CustomButton
                    >
                </div>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { required, requiredIf, email } from "vuelidate/lib/validators";
import { VueTelInput } from "vue-tel-input";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import { mapGetters, mapActions, mapMutations } from "vuex";
export default {
    props: {
        value: Boolean,
        email: String
    },

    data: () => ({
        mobileInputProps: {
            inputOptions: {
                type: "tel",
                placeholder: "phone number"
            },
            dropdownOptions: {
                showDialCodeInSelection: false,
                showFlags: true,
                showDialCodeInList: true
            },
            autoDefaultCountry: false,
            validCharactersOnly: true,
            mode: "international"
        },
        form: {
            email: "",
            phone: "",
            code: "",
            invalidPhone: true,
            showInvalidPhone: false
        },
        loading: false,
        resendLoading: false
    }),
    components: {
        VueTelInput,
        CustomButton,
        CustomInput
    },
    validations: {
        form: {
            email: {
                requiredIf: requiredIf(function () {
                    return (
                        this.authSettings.customer_login_with == "email" ||
                        (this.authSettings.customer_login_with == "email_phone" &&
                            this.authSettings.customer_otp_with == "email")
                    );
                }),
                email
            },
            phone: {
                requiredIf: requiredIf(function () {
                    return (
                        this.authSettings.customer_login_with == "phone" ||
                        (this.authSettings.customer_login_with == "email_phone" &&
                            this.authSettings.customer_otp_with == "phone")
                    );
                })
            },
            code: {
                required
            }
        }
    },
    computed: {
        ...mapGetters("auth", ["authSettings"]),
        ...mapGetters("app", ["availableCountries"]),
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
            return errors;
        },
        codeErrors() {
            const errors = [];
            if (!this.$v.form.code.$dirty) return errors;
            !this.$v.form.code.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        showVerifyAccount: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit("input", value);
            }
        }
    },
    mounted() {
        this.form.email = this.email;
    },
    methods: {
        ...mapActions("auth", {
            actionLogin: "login"
        }),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog"]),
        ...mapActions("app", ["fetchProductQuerries"]),
        ...mapActions("wishlist", ["fetchWislistProducts"]),
        ...mapActions("cart", ["fetchCartProducts"]),
        closeVerify(){
            this.showVerifyAccount = false;
        }, 
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async verifyAccount() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            if (
                (this.authSettings.customer_login_with == "phone" ||
                    (this.authSettings.customer_login_with == "email_phone" &&
                        this.authSettings.customer_otp_with == "phone")) &&
                this.form.invalidPhone
            ) {
                this.form.showInvalidPhone = true;
                return;
            }
            this.form.phone = this.form.phone.replace(/\s/g, "");

            this.loading = true;
            const res = await this.call_api("post", "auth/verify", this.form);
            if (res.data.success) {
                this.actionLogin(res.data);
                this.showLoginDialog(false);
                this.updateChatWindow(false);

                this.fetchWislistProducts();
                this.fetchProductQuerries();
                this.fetchCartProducts();

                this.$router.push(this.$route.query.redirect || { name: "Cart" });
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }
            this.loading = false;
        },
        async resendCode() {
            this.$v.form.email.$touch();
            if (this.$v.form.email.$anyError) {
                return;
            }
            if (
                (this.authSettings.customer_login_with == "phone" ||
                    (this.authSettings.customer_login_with == "email_phone" &&
                        this.authSettings.customer_otp_with == "phone")) &&
                this.form.invalidPhone
            ) {
                this.form.showInvalidPhone = true;
                return;
            }
            this.form.phone = this.form.phone.replace(/\s/g, "");

            this.resendLoading = true;
            const res = await this.call_api("post", "auth/resend-code", this.form);

            if (res.data.success) {
                this.snack({
                    message: res.data.message
                });
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }
            this.resendLoading = false;
        }
    }
};
</script>

<style lang="scss">
.modal-register {
    max-width: 550px;
}
</style>

<style lang="scss" scoped>
.modal-register {
    &-card {
        border-radius: 10px;
    }

    .v-stepper__header {
        box-shadow: none;

        .v-divider {
            border-color: rgba(#707070, 0.5) !important;
            border-width: 2px 0 0;

            &.step-active {
                border-color: #707070 !important;
            }
        }
    }

    &-subheader {
        width: 92%;
        margin: auto;
        border-width: 2px 0 0;
    }

    .v-stepper {
        box-shadow: none;

        &.theme--light {
            &::v-deep {
                .v-stepper__step {
                    &:not(.v-stepper__step--active) {
                        .v-stepper__step__step {
                            background: rgba(#000000, 0.25);
                        }
                    }

                    &.v-stepper__step--complete {
                        .v-stepper__step__step {
                            background: rgba(0, 0, 0, 0.87);
                        }
                    }
                }
            }
        }
    }

    &-step {
        &::v-deep {
            .v-stepper__step__step {
                font-size: 15px;
                line-height: 1;
                width: 27px;
                height: 27px;
                padding-top: 5px;
                margin-right: 0;
            }
        }
    }
}

.v-divider {
    border-color: #e4e4e4 !important;
}

.vue-tel-input {
    background: #f5f5f5;
    border-color: #f5f5f5;
    height: 38px;
    border-radius: 5px;

    &:focus-within {
        box-shadow: none;
    }

    &.error--text {
        border-color: currentColor;
        border-width: 2px;
    }

    &:hover {
        background: #dfdfdf;
    }
}

.theme--light {
    .v-input {
        &::v-deep {
            .v-input__slot {
                background: #f5f5f5;

                &:hover {
                    background: #dfdfdf;
                }
            }
        }

        &.v-text-field--outlined {
            &::v-deep {
                &:not(.v-input--has-state) {
                    .v-input__slot {
                        &:hover,
                        fieldset {
                            border-color: #f5f5f5;
                        }
                    }
                }
            }
        }
    }
}
.esc-button{
    position: absolute;
    top: 25%;
    right: 5%;
    padding: 0;
    margin: 0;
    &:hover{
        path{
            fill: #fcd6ba;
        }
    }
}
</style>
