<template>
    <div class="forgot-password d-flex flex-column h-100">
        <v-container class="d-flex flex-grow-1">
            <v-row justify="center" align="center">
                <v-col cols="12" sm="10" md="8" lg="6">
                    <div class="wrap pa-3 pa-sm-5 mb-10">
                        <div class="forgot-password-content pa-3 pa-sm-5 pt-5 pt-sm-8">
                            <h1 class="forgot-password-title">¿Olvidó su contraseña?</h1>
                            <v-divider class="my-4" />
                            <p>
                                Incluye Lorem Ipsum is simply dummy text of the printing • Lorem Ipsum has been the
                                industry's • Incluye Lorem Ipsum is simply dummy text of the printing • Lorem Ipsum has
                                been the industry's • Incluye Lorem Ipsum is simply dummy text.
                            </p>
                            <div class="inputs mb-5">
                                <label class="black--text text-uppercase">{{ $t("email_address") }}</label>
                                <CustomInput
                                    type="email"
                                    v-model="form.email"
                                    :error-messages="emailErrors"
                                    hide-details="auto"
                                    required
                                />
                            </div>
                            <CustomButton
                                block
                                color="black"
                                text="Enviar"
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
import { required, email } from "vuelidate/lib/validators";

import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";

export default {
    data: () => ({
        form: { email: "" },
        loading: false
    }),
    components: {
        CustomButton,
        CustomInput
    },
    validations: {
        form: {
            email: {
                required,
                email
            }
        }
    },
    computed: {
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
            return errors;
        }
    },
    methods: {
        async resetPassword() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }

            this.loading = true;

            const res = await this.call_api("post", "auth/password/create", this.form);

            if (res.data.success) {
                this.$router.push({ name: "NewPassword", params: { email: this.form.email } });

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
