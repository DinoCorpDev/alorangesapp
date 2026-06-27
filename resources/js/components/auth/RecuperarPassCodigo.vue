<template>
    <v-dialog content-class="modal-register" v-model="showRecuperarPass">
        <v-card class="modal-register-card">
            <v-card-title v-if="numberPag == 1" style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="text-xs-center justify-center title white--text font-weight-bold">
                Código de verificación
            </v-card-title>
            <v-card-title v-if="numberPag == 2" style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="text-xs-center justify-center title white--text font-weight-bold">
                Elige una contraseña nueva
            </v-card-title>
            <div class="forgot-password d-flex flex-column h-100">
                <v-stepper v-model="numberPag">
                    <v-stepper-header class="d-none">
                        <v-stepper-step
                            :class="numberPag > 1 ? 'v-stepper__step--complete' : ''"
                            class="modal-register-step"
                            step="1"
                        />

                        <v-divider :class="numberPag > 1 ? 'step-active' : ''" />

                        <v-stepper-step
                            :class="numberPag > 2 ? 'v-stepper__step--complete' : ''"
                            class="modal-register-step"
                            step="2"
                        />
                    </v-stepper-header>


                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <v-container class="">
                                <div class="forgot-password-content">
                                    <p>
                                        Digita el código que enviamos a tu cuenta asociada par cambiar la contraseña.
                                    </p>
                                    <div class="inputs mb-4">
                                        <label class="black--text">Código</label>
                                        <CustomInput
                                            v-model="form.code"
                                            length="6"
                                            block
                                            type="number"
                                            :error-messages="codeErrors"
                                            placeholder="Ingresar código de seguridad"
                                            class="place-holder"
                                            hide-details="auto"
                                            :disabled="loading"
                                            required
                                        ></CustomInput>
                                    </div>
                                </div>
                            </v-container>
                        </v-stepper-content>

                        <v-stepper-content step="2">
                            <v-container class="d-flex flex-grow-1">
                                <div class="forgot-password-content">
                                    <p>
                                        Asegurate de que tu contraseña tenga 8 caracteres o mas. Intenta que incluya
                                        números, letras y signos de putuacion para que sea una <b>contraseña segura.</b>
                                    </p>
                                    <p>
                                        Una vez que cambies tu contraseña, se cerrarán todas tus sesiones activas de
                                        Aloranges.
                                    </p>
                                    <div class="inputs mb-4">
                                        <label class="black--text">Introduce una contraseña nueva</label>
                                        <v-text-field
                                            :type="passwordShow ? 'text' : 'password'"
                                            placeholder="Contraseña"
                                            v-model="form.password"
                                            :error-messages="passwordErrors"
                                            :append-icon="passwordShow ? 'las la-eye' : 'las la-eye-slash'"
                                            class="input-group--focused place-holder"
                                            @click:append="passwordShow = !passwordShow"
                                            hide-details="auto"
                                            required
                                            dense
                                            outlined
                                        />
                                    </div>

                                    <div class="inputs mb-2">
                                        <label class="black--text">Confirma tu contraseña</label>
                                        <v-text-field
                                            :type="passwordShow ? 'text' : 'password'"
                                            placeholder="Contraseña"
                                            :append-icon="passwordShow ? 'las la-eye' : 'las la-eye-slash'"
                                            class="input-group--focused place-holder"
                                            @click:append="passwordShow = !passwordShow"
                                            v-model="form.confirmPassword"
                                            :error-messages="confirmPasswordErrors"
                                            hide-details="auto"
                                            required
                                            dense
                                            outlined
                                        />
                                    </div>
                                </div>
                            </v-container>
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </div>

            <v-card-actions class="pa-5 d-block">
                <CustomButton v-if="numberPag < 2" text="Continuar" color="orange" block type="button" @click.stop="after" />

                <CustomButton
                    v-if="numberPag == 2"
                    text="Cambiar Contraseña"
                    color="orange"
                    block
                    type="submit"
                    @click="resetPassword"
                    :disabled="loadingregister"
                    :loadingregister="loadingregister"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { required, email, minLength, sameAs, requiredIf } from "vuelidate/lib/validators";

import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

// Custom validators
const isTrue = value => value === true;

export default {
    props: {
        value: Boolean,
        email: String
    },

    data: () => ({
        form: {
            email: "",
            code: "",
            password: "",
            confirmPassword: ""
        },
        loading: false,
        loadingregister: false,
        numberPag: 1,
        passwordShow: false
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
            code: {
                required
            },
            password: { required, minLength: minLength(6) },
            confirmPassword: { required, sameAsPassword: sameAs("password") }
        }
    },
    computed: {
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
        },
        showRecuperarPass: {
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
        async after() {
            return (this.numberPag += 1);
        },
        async before() {
            return (this.numberPag -= 1);
        },
        async resetPassword() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }

            this.form.code = this.form.code.replace(/\s/g, "");
            this.loading = true;
            const res = await this.call_api("post", "auth/password/reset", this.form);
            if (res.data.success) {
                this.$router.push({ name: "Home2" });
                this.snack({
                    message: res.data.message
                });
                this.showRecuperarPass = false;
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }
            this.loading = false;
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

.v-input {
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
</style>
