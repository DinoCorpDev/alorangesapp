<template>
    <div class="login d-flex flex-column h-100">
        <v-container class="flex-grow-1 pa-0" fluid>
            <v-row class="wrap" no-gutters>
                <v-col cols="12" lg="6">
                    <CarouselLogin :slides="sliderItems" title="" />
                </v-col>
                <v-col cols="12" lg="6" class="pt-lg-0">
                    <div class="login-content h-100 pa-lg-5 pa-3 pt-lg-8">
                        <div class="inputs mb-8">
                            <div class="mt-lg-6">
                                <h1 class="login-title">Inicio de sesión</h1>
                                <v-divider class="my-lg-4 my-2" />
                            </div>
                            <v-form ref="loginForm" lazy-validation @submit.prevent="login()">
                                <div class="form-group mb-6">
                                    <label class="black--text">Usuario registrado</label>
                                    <CustomInput
                                        placeholder="Correo electrónico o número de teléfono"
                                        v-model="form.email"
                                        type="text"
                                        :error-messages="emailErrors"
                                        required
                                    />
                                </div>
                                <div class="form-group mb-6">
                                    <label class="black--text">Escribir contraseña</label>
                                    <!-- <CustomInput
                                        placeholder="Contraseña"
                                        v-model="form.password"
                                        type="password"
                                        :error-messages="passwordErrors"
                                        :append-icon="passwordShow ? 'las la-eye' : 'las la-eye-slash'"
                                        @click:append="passwordShow = !passwordShow"
                                        required
                                    /> -->
                                    <v-text-field
                                        v-model="form.password"
                                        placeholder="Contraseña"
                                        :error-messages="passwordErrors"
                                        :type="passwordShow ? 'text' : 'password'"
                                        :append-icon="passwordShow ? 'las la-eye-slash' : 'las la-eye'"
                                        class="input-group--focused place-holder"
                                        hide-details="auto"
                                        required
                                        dense
                                        outlined
                                        @click:append="passwordShow = !passwordShow"
                                    ></v-text-field>
                                </div>
                                <div class="d-flex">
                                    <p class="black--text mr-4" style="font-size: 13px">¿Has olvidado tu contraseña?</p>
                                    <a @click="showModalRecuperarPass" class="login-link">
                                        <b>Ir a recuperar contraseña</b>
                                    </a>
                                </div>
                                <CustomButton
                                    :disabled="loading"
                                    :loading="loading"
                                    text="Iniciar sesión"
                                    block
                                    class="mt-4 mb-8"
                                    color="orange"
                                    type="submit"
                                    @click="login"
                                    style="width: 100%"
                                />
                            </v-form>
                            <div class="d-flex">
                                <p class="black--text link-forgot mr-4" style="font-size: 13px">
                                    ¿No tienes ninguna cuenta?
                                </p>
                                <a @click="showModalRegister" class="login-link">
                                    <b>Registrarse</b>
                                </a>
                            </div>

                            <!-- <a @click="showModalRegister" class="black--text link-custom">
                                ¿No tienes ninguna cuenta?
                            </a>
                            <CustomButton
                                block
                                light
                                color="grey"
                                class="mt-4 mb-4"
                                text="Registrarse"
                                @click="showModalRegister"
                            /> -->
                            <p class="black--text link-custom1">
                                Al registrarte, aceptas los <b>Términos de servicios</b> y la
                                <b>Política de privacidad</b>, incluida la politica de <b>Uso de Cookies</b>
                            </p>
                        </div>
                        <AuthFooter />
                    </div>
                </v-col>
            </v-row>

            <ModalRegister v-model="showRegister" />
            <RecuperarPass v-model="showRecuperarPass" />
        </v-container>
    </div>
</template>

<script>
import { required, email } from "vuelidate/lib/validators";
import { mapActions, mapGetters, mapMutations } from "vuex";

import AuthFooter from "./AuthFooter.vue";
import ArrowForget from "../../components/icons/ArrowForget.vue";
import CarouselLogin from "../../components/global/CarouselLogin.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import ModalRegister from "../../components/user/ModalRegister.vue";
import RecuperarPass from "../../components/auth/RecuperarPass.vue";

export default {
    components: {
        AuthFooter,
        CarouselLogin,
        CustomButton,
        CustomInput,
        ModalRegister,
        RecuperarPass,
        ArrowForget
    },
    data: () => ({
        form: {
            email: "",
            password: ""
        },
        loading: false,
        showRegister: false,
        passwordShow: false,
        showRecuperarPass: false,
        sliderItems: [
            {
                src: "/public/assets/img/login1.png",
                type: "image"
            },
            {
                src: "/public/assets/img/login2.png",
                type: "image"
            }
        ]
    }),
    validations: {
        form: {
            email: {
                required,
                email
            },
            password: {
                required
            }
        }
    },
    computed: {
        ...mapGetters("app", ["generalSettings"]),
        ...mapGetters("cart", ["getTempUserId"]),
        ...mapGetters("auth", ["authSettings"]),
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("this_field_is_required"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("this_field_is_required_a_valid_email"));
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.required && errors.push(this.$i18n.t("this_field_is_required"));
            return errors;
        }
    },
    methods: {
        ...mapActions("auth", { actionLogin: "login" }),
        ...mapActions("app", ["fetchProductQuerries"]),
        ...mapActions("wishlist", ["fetchWislistProducts"]),
        ...mapActions("cart", ["fetchCartProducts"]),
        ...mapMutations("cart", ["removeTempUserId"]),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog"]),

        async login() {
            this.$v.form.$touch();

            if (this.$v.form.$anyError) {
                return;
            }

            if (this.getTempUserId) {
                this.form.temp_user_id = this.getTempUserId;
            }

            this.loading = true;

            try {
                const res = await this.call_api("post", "auth/login", this.form);

                if (res.data.success) {
                    if (res.data.verified === true || this.authSettings.customer_otp_with === "disabled") {
                        if (this.getTempUserId) {
                            this.removeTempUserId();
                        }

                        this.actionLogin(res.data);
                        this.showLoginDialog(false);
                        this.updateChatWindow(false);
                        this.fetchWislistProducts();
                        this.fetchProductQuerries();
                        this.fetchCartProducts();
                        this.$router.push(this.$route.query.redirect || { name: "Cart" });
                    } else {
                        if (this.authSettings.customer_login_with === "email") {
                            this.$router.push({
                                name: "VerifyAccount",
                                params: { email: this.form.email }
                            });
                        }
                    }

                    this.snack({ message: res.data.message });
                } else {
                    this.snack({ message: res.data.message, color: "red" });
                }
            } catch (error) {
                this.snack({
                    message: "Error inesperado. Intenta de nuevo.",
                    color: "red"
                });
            } finally {
                this.loading = false;
            }
        },
        async showModalRegister() {
            this.showLoginDialog(false);
            return (this.showRegister = true);
        },
        async showModalRecuperarPass() {
            this.showLoginDialog(false);
            return (this.showRecuperarPass = true);
        }
    }
};
</script>

<style lang="scss">
* {
    &::-webkit-scrollbar {
        width: 3px;
    }

    &::-webkit-scrollbar-track {
        background: #b5b5b5;
        border-radius: 1px;
    }

    &::-webkit-scrollbar-thumb {
        background: rgba(#5a5a5a, 0.8);
        border-radius: 1px;
    }

    &::-webkit-scrollbar-thumb:hover {
        background: #5a5a5a;
    }
}
</style>

<style lang="scss" scoped>
.login {
    /* width */

    &-title {
        font-size: 24px;
        font-weight: 500;
        letter-spacing: 0;
        line-height: 30px;

        @media (max-width: 600px) {
            font-size: 17px;
            line-height: 20px;
        }
    }

    &-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
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

.wrap {
    background-color: #fafcfc;
    border-radius: 10px;
}

.v-divider {
    border-color: #e4e4e4 !important;
    height: 2px;
}

.form-group {
    label {
        font-family: "Overpass", sans-serif;
        font-size: 12px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
}

.link-custom {
    font-size: var(--font-size-caption);
    letter-spacing: 0.4px;
    text-transform: uppercase;
}

.link-custom1 {
    font-size: var(--font-size-caption);
    letter-spacing: 0.4px;
}

.link-forgot {
    font-size: var(--font-size-caption);
    letter-spacing: 0.4px;
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

.login-link {
    font-size: 13px;
    color: #f58634;

    &:hover {
        color: #fbd6bb;
    }
}
</style>
