<template>
    <v-dialog v-model="isVisible" max-width="960px" @click:outside="closeDialog">
        <div class="white pa-8 rounded">
            <v-form class="inputs" ref="loginForm" lazy-validation @submit.prevent="register()">
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">
                            {{ $t("old password") }}
                        </span>
                        <custom-input
                            v-model="form.oldPassword"
                            :error-messages="oldPasswordErrors"
                            @blur="$v.form.oldPassword.$touch()"
                            type="password"
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">
                            {{ $t("new password") }}
                        </span>
                        <custom-input
                            v-model="form.password"
                            :error-messages="oldPasswordErrors"
                            @blur="$v.form.oldPassword.$touch()"
                            type="password"
                            required
                        />
                        <custom-button
                            block
                            class="mt-5"
                            text="Guardar"
                            type="submit"
                            color="black"
                            @click="register"
                            :disabled="infoUpdateLoading"
                            :loading="infoUpdateLoading"
                        />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-dialog>
</template>

<script>
import { required, requiredIf, email, minLength, sameAs } from "vuelidate/lib/validators";
import { mapActions, mapGetters, mapMutations } from "vuex";
import { VueTelInput } from "vue-tel-input";

import CustomButton from "../global/CustomButton.vue";
import CustomInput from "../global/CustomInput.vue";
import SelectCustom from "../global/SelectCustom.vue";

export default {
    props: {
        show: { type: Boolean, required: true, default: false },
        oldProfile: { type: Object, default: () => {} }
    },
    components: {
        VueTelInput,
        CustomButton,
        CustomInput,
        SelectCustom
    },
    data: () => ({
        form: {
            password: "",
            oldPassword: "",
        },
        infoUpdateLoading: false
    }),
    validations: {
        form: {
            password: { required },
            oldPassword: { required }
        }
    },
    watch: {
        oldProfile(newVal, oldVal) {
            if (newVal && !this.is_empty_obj(newVal)) {
                this.processOldProfile(newVal);
            } else {
                this.resetData();
            }
        }
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapGetters("app", ["generalSettings", "availableCountries"]),
        isVisible: {
            get: function () {
                return this.show;
            },
            set: function (newValue) {}
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        oldPasswordErrors() {
            const errors = [];
            if (!this.$v.form.oldPassword.$dirty) return errors;
            !this.$v.form.oldPassword.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        }
    },
    methods: {
        ...mapActions("auth", ["login"]),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog", "setUser"]),
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async register() {
            this.$v.form.$touch();

            this.infoUpdateLoading = true;

            const res = await this.call_api("post", "user/info/updatePassword", this.form);

            if (res.data.success) {
                this.setUser(res.data.user);
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }

            this.infoUpdateLoading = false;
        },
        closeDialog() {
            this.isVisible = false;
            this.$emit("close");
        }
    }
};
</script>
