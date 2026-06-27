<template>
    <div class="forgot-password d-flex flex-column h-100">
        <v-container class="d-flex flex-grow-1">
            <v-row justify="center" align="center">
                <v-col cols="12" sm="12" md="8" lg="6">
                    <div class="wrap pa-3 pa-sm-5 mb-10">
                        <div class="forgot-password-content pa-3 pa-sm-5 pt-5 pt-sm-8">
                            <h1 class="forgot-password-title">Restablecer contraseña</h1>

                            <v-divider class="my-4" />

                            <p v-if="resetWith == 'email'">
                                {{ $t("a_verification_code_has_been_sent_to_your_email") }}
                            </p>

                            <p v-else>
                                {{ $t("a_verification_code_has_been_sent_to_your_phone_number") }}
                            </p>

                            <p v-if="resetWith == 'email'">
                                {{ $t("enter_your_email_address_code__new_password") }}
                            </p>
                            <p v-else>
                                {{ $t("enter_your_phone_number_code__new_password") }}
                            </p>

                            <div class="inputs mb-5">
                                <label class="black--text text-uppercase"
                                    >Correo electrónico o número de teléfono</label
                                >
                                <CustomInput
                                    placeholder="Correo electrónico o número de teléfono"
                                    class="place-holder"
                                    type="email"
                                    v-model="form.email"
                                    :error-messages="emailErrors"
                                    hide-details="auto"
                                    required
                                />
                            </div>

                            <div class="inputs mb-5">
                                <label class="black--text text-uppercase">{{ $t("code") }}</label>
                                <CustomInput
                                    type="email"
                                    v-model="form.code"
                                    :error-messages="codeErrors"
                                    hide-details="auto"
                                    required
                                />
                            </div>

                            <div class="inputs mb-5">
                                <label class="black--text text-uppercase">{{ $t("password") }}</label>
                                <CustomInput
                                    type="email"
                                    v-model="form.password"
                                    :error-messages="passwordErrors"
                                    hide-details="auto"
                                    required
                                />
                            </div>

                            <div class="inputs mb-5">
                                <label class="black--text text-uppercase">{{ $t("confirm_password") }}</label>
                                <CustomInput
                                    type="email"
                                    v-model="form.confirmPassword"
                                    :error-messages="confirmPasswordErrors"
                                    hide-details="auto"
                                    required
                                />
                            </div>

                            <CustomButton
                                block
                                color="black"
                                text="Cambiar contraseña"
                                type="submit"
                                @click="resetPassword"
                                :loading="loading"
                                :disabled="loading"
                            />
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import { required, email, minLength, sameAs, requiredIf } from "vuelidate/lib/validators";

import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

export default {
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
            code: "",
            password: "",
            confirmPassword: "",
            invalidPhone: true,
            showInvalidPhone: false
        },
        resetWith: "email",
        loading: false
    }),
    components: {
        CustomButton,
        CustomInput
    },
    validations: {
        form: {
            email: {
                requiredIf: requiredIf(function () {
                    return this.resetWith == "email";
                }),
                email
            },
            phone: {
                requiredIf: requiredIf(function () {
                    return this.resetWith == "phone";
                })
            },
            code: {
                required
            },
            password: { required, minLength: minLength(6) },
            confirmPassword: { required, sameAsPassword: sameAs("password") }
        }
    },
    computed: {
        ...mapGetters("app", ["availableCountries"]),
        ...mapGetters("auth", ["authSettings"]),
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
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.password.minLength && errors.push(this.$i18n.t("password_must_be_minimum_6_characters"));
            return errors;
        },
        confirmPasswordErrors() {
            const errors = [];
            if (!this.$v.form.confirmPassword.$dirty) return errors;
            !this.$v.form.confirmPassword.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.confirmPassword.sameAsPassword &&
                errors.push(this.$i18n.t("password_and_confirm_password_should_match"));
            return errors;
        }
    },
    methods: {
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async resetPassword() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            if (this.resetWith == "phone" && this.form.invalidPhone) {
                this.form.showInvalidPhone = true;
                return;
            }
            this.form.code = this.form.code.replace(/\s/g, "");
            this.loading = true;
            const res = await this.call_api("post", "auth/password/reset", this.form);
            if (res.data.success) {
                this.$router.push({ name: "Login" });
                this.snack({
                    message: res.data.message
                });
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }
            this.loading = false;
        }
    },
    created() {
        if (this.$route.params.email) {
            this.form.email = this.$route.params.email;
        }
        if (this.$route.params.phone) {
            this.form.phone = this.$route.params.phone;
            this.resetWith = "phone";
        } else if (
            this.authSettings.customer_login_with == "phone" ||
            (this.authSettings.customer_login_with == "email_phone" && this.authSettings.customer_otp_with == "phone")
        ) {
            this.resetWith = "phone";
        }
    }
};
</script>

<style lang="scss">
.v-application {
    &.theme--light {
        background: #dee0e0;
    }
}
</style>

<style lang="scss" scoped>
.forgot-password {
    &-title {
        font-size: 17px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 22px;

        @media (min-width: 600px) {
            font-size: 24px;
            line-height: 30px;
        }
    }

    &-content {
        border: 1px solid #e4e4e4;
        border-radius: 10px;

        p {
            font-size: 12px;
            letter-spacing: 0.5px;
            line-height: 18px;

            @media (min-width: 600px) {
                font-size: 15px;
            }
        }
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

.place-holder {
    font-family: "Roboto", sans-serif;
    font-size: 15px;
    letter-spacing: 0.5px;

    &::v-deep {
        .v-input__control,
        .v-input__slot {
            min-height: 38px;
        }

        ::placeholder {
            visibility: hidden;
        }

        :hover::placeholder {
            visibility: visible;
            font-style: italic;
        }
        &:not(.v-input--has-state) {
            .v-input__slot fieldset {
                color: #dfdfdf;
            }
        }
    }
}

.wrap {
    background-color: #fafcfc;
    border-radius: 10px;
}

.v-divider {
    border-color: #e4e4e4 !important;
}

.inputs {
    margin-top: 20px;
}
</style>
