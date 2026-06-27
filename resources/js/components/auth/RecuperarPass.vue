<template>
    <v-dialog content-class="modal-register" v-model="showRecuperarPass">
        <v-card class="modal-register-card">
            <v-card-title v-if="numberPag == 1" style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="text-xs-center justify-center title white--text font-weight-bold">
                ¿Olvidó su contraseña?
            </v-card-title>
            <v-card-title v-if="numberPag == 3" style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="text-xs-center justify-center title white--text font-weight-bold">
                ¿A dónde debemos enviar el código de confirmación?
            </v-card-title>
            <v-card-title v-if="numberPag == 2" style="background-color: #f58634; color:white; position: relative; padding: 12px 0;" class="text-xs-center justify-center title white--text font-weight-bold">
                Confirma tu nombre de usuario
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

                        <v-divider :class="numberPag > 2 ? 'step-active' : ''" />

                        <v-stepper-step class="modal-register-step" step="3" />
                    </v-stepper-header>

                    <v-divider class="modal-register-subheader" />

                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <v-container class="d-flex flex-grow-1 pa-0">
                                <div class="">
                                    <p>
                                        Introduce el correo electrónico asociado a tu cuenta para
                                        poder cambiar tu contraseña.
                                    </p>
                                    <!-- <p>
                                        Introduce el correo electrónico o número de teléfono asociados a tu cuenta para
                                        poder cambiar tu contraseña.
                                    </p> -->
                                    <div class="inputs mb-5">
                                        <label class="black--text text-uppercase"
                                            >Correo electrónico</label
                                        >
                                        <!-- <label class="black--text text-uppercase"
                                            >Correo electrónico o número de teléfono</label
                                        > -->
                                        <CustomInput
                                            placeholder="Correo electrónico"
                                            class="place-holder"
                                            type="email"
                                            v-model="form.email"
                                            :error-messages="emailErrors"
                                            hide-details="auto"
                                            required
                                        />
                                        <!-- <CustomInput
                                            placeholder="Correo electrónico o número de teléfono"
                                            class="place-holder"
                                            type="email"
                                            v-model="form.email"
                                            :error-messages="emailErrors"
                                            hide-details="auto"
                                            required
                                        /> -->
                                    </div>
                                </div>
                            </v-container>
                        </v-stepper-content>
                    </v-stepper-items>
                    <v-stepper-items>
                        <v-stepper-content step="2">
                            <v-container class="d-flex flex-grow-1 pa-0">
                                <div class="">
                                    <p>
                                        Confirma tu identida introduciendo el nombre de usuario asociado a tu cuenta de
                                        © Alorange.
                                    </p>
                                    <v-row>
                                        <v-col cols="12" md="6" class="inputs mb-5">
                                            <label class="black--text text-uppercase">PRIMER NOMBRE</label>
                                            <CustomInput
                                                placeholder="Ingresar primer nombre"
                                                class="place-holder"
                                                type="text"
                                                v-model="form.first_name"
                                                :error-messages="firstNameErrors"
                                                hide-details="auto"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6" class="inputs mb-5">
                                            <label class="black--text text-uppercase">PRIMER APELLIDO</label>
                                            <CustomInput
                                                placeholder="Ingresar primer apellido"
                                                class="place-holder"
                                                type="text"
                                                v-model="form.first_lastname"
                                                :error-messages="firstLastNameErrors"
                                                hide-details="auto"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-container>
                        </v-stepper-content>
                    </v-stepper-items>
                    <v-stepper-items>
                        <v-stepper-content step="3">
                            <v-container class="d-flex flex-grow-1 pa-0">
                                <div class="">
                                    <p>
                                        Antes de permitirte cambiar tu contraseña, tenemos que asegurarnos de que
                                        realmente eres tú.
                                    </p>
                                    <p>Empieza por seleccionar adónde enviaremos un código de confirmación.</p>
                                    <v-row class="mb-2">
                                        <v-col cols="12" sm="12">
                                            <CustomCheckbox
                                                inputValue="Natural"
                                                label="Enviar un correo electrónico a ******@*****.*om"
                                                name="personType"
                                                type="radio"
                                            />
                                        </v-col>
                                        <!-- <v-col cols="12" sm="12">
                                            <CustomCheckbox
                                                inputValue="Juridical"
                                                label="Enviar un mensaje de texto al ***********"
                                                name="personType"
                                                type="radio"
                                                disabled
                                            />
                                        </v-col> -->
                                    </v-row>
                                    <p>Comunícate con el <b>Soporte de © Alorange</b> si no tienes acceso.</p>
                                </div>
                            </v-container>
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </div>

            <v-card-actions class="pa-5 d-flex">
                <CustomButton
                    v-if="numberPag < 3"
                    text="siguiente"
                    color="orange"
                    type="button"
                    class="mb-2 ml-2"
                    @click.stop="after"
                />
                <CustomButton
                    v-if="numberPag == 3"
                    text="Enviar código"
                    color="orange"
                    type="submit"
                    class="mb-2"
                    @click.stop="resetPassword"
                    :disabled="loadingregister"
                    :loadingregister="loadingregister"
                />
                <CustomButton text="Cancelar" color="orange2" type="button" class="ml-2 mb-2" @click="cerrarModal" />
            </v-card-actions>
        </v-card>
        <v-dialog v-model="userNotFound" width="500">
            <v-card>
                <i class="las la-times" style="color: grey; font-size: 20px; right: 0;" @click="userNotFound = false"></i>
                <div align="center" style="height: 300px; margin-top: 50px; margin-right: 20px; margin-left: 20px">
                    
                    <br /><br />

                    <h4 align="center">¡ERROR!</h4>
                    <br />
                    <i class="las la-times" style="color: red; font-size: 80px"></i>
                    <br />
                    <p>
                        Los campos no coinciden.
                    </p>
                </div>
                
            </v-card>
        </v-dialog>
    </v-dialog>
</template>

<script>
import { required, email } from "vuelidate/lib/validators";
import CustomCheckbox from "../../components/global/CustomCheckbox.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

// Custom validators
const isTrue = value => value === true;

export default {
    props: {
        value: Boolean
    },

    data: () => ({
        form: { email: "" },
        loading: false,
        loadingregister: false,
        numberPag: 1,
        userNotFound: false
    }),
    components: {
        CustomButton,
        CustomInput,
        CustomCheckbox
    },
    validations: {
        form: {
            email: {
                required,
                email
            },
            first_name: {
                required
            },
            first_lastname: {
                required
            }
        }
    },
    created() {
        this.numberPag = 1;
    },
    mounted() {
        this.numberPag = 1;
    },
    computed: {
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
            return errors;
        },
        firstNameErrors() {
            const errors = [];
            if (!this.$v.form.first_name.$dirty) return errors;
            !this.$v.form.first_name.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        firstLastNameErrors() {
            const errors = [];
            if (!this.$v.form.first_lastname.$dirty) return errors;
            !this.$v.form.first_lastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
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
    methods: {
        async after() {
            if (this.numberPag == 1 && this.form.email == "") {
                this.snack({
                    message: "Por favor ingresa el correo",
                    color: "red"
                });
                return;
            }

            if (this.numberPag == 2) {
                let formData = new FormData();

                formData.append("email", this.form.email);
                formData.append("first_name", this.form.first_name);
                formData.append("first_lastname", this.form.first_lastname);

                const res = await this.call_api("post", "auth/verify-data", formData);

                if (res.data.result) {
                    return (this.numberPag += 1);
                } else {
                    this.userNotFound = true;
                    // this.snack({
                    //     message: res.data.message,
                    //     color: "red"
                    // });
                    return;
                }
            }
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

            this.loading = true;

            const res = await this.call_api("post", "auth/password/create", this.form);

            if (res.data.success) {
                this.showRecuperarPass = false;
                window.location.href = "/?modal=Password&email=" + this.form.email;
                // this.$router.replace({ name: 'Home2', query: { modal: "Password", email: this.form.email }});

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
        },
        resetData() {
            this.form.email = "";
            this.form.first_name = "";
            this.form.first_lastname = "";
        },
        cerrarModal() {
            this.showRecuperarPass = false;
            this.numberPag = 1;
            this.resetData();
        }
    }
};
</script>

<style lang="scss">
.modal-register {
    max-width: 550px;
}
.custom-checkbox {
    &-checkmark {
        margin-left: 5px;
    }
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

.custom-checkbox {
    padding-left: 40px;
    border: 1px solid;
    border-radius: 5px;
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
</style>
