<template>
    <div
        class="
            rounded-lg
            pa-5
            border border-gray-200
            overflow-hidden
            white
            shadow-light
        "
    >
        <v-row no-gutters align="center">
            <v-col cols="12" lg="6" order="2" order-lg="1" class="">
                <banner
                    :loading="false"
                    :banner="$store.getters['app/banners'].login_page"
                    class="mt-5 mt-lg-0"
                />
            </v-col>
            <v-col cols="12" order="1" order-lg="2" lg="6">
                <div class="px-lg-7">
                    <h1 class="text-uppercase lh-1 mb-6">
                        <span class="opacity-50 fs-22 fw-400">{{
                            $t("login_with")
                        }}</span>
                        <span class="d-block display-1 fw-900 primary--text">{{
                            $store.getters["app/appName"]
                        }}</span>
                        <span class="opacity-50 fs-22 fw-400">{{
                            $t("account")
                        }}</span>
                    </h1>
                    <v-form
                        ref="loginForm"
                        lazy-validation
                        @submit.prevent="login()"
                    >
                        <div v-if="authSettings.customer_login_with == 'email' || (!showPhoneField && authSettings.customer_login_with == 'email_phone')" class="mb-4">
                            <div class="mb-1 fs-13 fw-500">
                                {{ $t("email") }}
                            </div>
                            <v-text-field
                                v-model="form.email"
                                :placeholder="$t('email_address')"
                                type="text"
                                :error-messages="emailErrors"
                                hide-details="auto"
                                required
                                outlined
                            ></v-text-field>
                            <div v-if="authSettings.customer_login_with == 'email_phone'" class="text-end font-italic fs-12 opacity-70">
                                <span class="primary--text c-pointer" @click="showPhoneField = !showPhoneField" >{{ $t("use_phone_instead") }}</span>
                            </div>
                        </div>
                        <div v-if="authSettings.customer_login_with == 'phone' || (showPhoneField && authSettings.customer_login_with == 'email_phone')" class="mb-4">
                            <div class="mb-1 fs-13 fw-500">
                                {{ $t("phone_number") }}
                            </div>
                            <vue-tel-input
                                v-model="form.phone"
                                v-bind="mobileInputProps"
                                :only-countries="availableCountries"
                                @validate="phoneValidate"
                            >
                                <template #arrow-icon ><span class=" vti__dropdown-arrow " >&nbsp;▼</span ></template >
                            </vue-tel-input>
                            <div v-if="$v.form.phone.$error" class=" v-text-field__details mt-2 pl-3 " >
                                <div class=" v-messages theme--light error--text " role="alert">
                                    <div class=" v-messages__wrapper " >
                                        <div class=" v-messages__message " >{{ $t("Este campo es requerido") }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!$v.form.phone.$error && form.showInvalidPhone" class=" v-text-field__details mt-2 pl-3">
                                <div class=" v-messages theme--light error--text " role="alert" >
                                    <div class=" v-messages__wrapper " >
                                        <div class=" v-messages__message " >
                                            {{ $t("phone_number_must_be_valid") }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="authSettings.customer_login_with == 'email_phone'" class="text-end font-italic fs-12 opacity-70">
                                <span class="primary--text c-pointer" @click="showPhoneField = !showPhoneField" >{{ $t("use_phone_instead") }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 fs-13 fw-500">
                                {{ $t("password") }}
                            </div>
                            <v-text-field
                                v-model="form.password"
                                placeholder="* * * * * * * *"
                                :error-messages="passwordErrors"
                                :type="passwordShow ? 'text' : 'password'"
                                :append-icon="
                                    passwordShow
                                        ? 'las la-eye'
                                        : 'las la-eye-slash'
                                "
                                class="input-group--focused"
                                hide-details="auto"
                                required
                                outlined
                                @click:append="passwordShow = !passwordShow"
                            ></v-text-field>
                            <div class="text-end font-italic fs-12 opacity-70">
                                <router-link
                                    :to="{ name: 'ForgotPassword' }"
                                    class="primary--text"
                                >{{ $t("forgot_password") }}?</router-link
                                >
                            </div>
                        </div>
                        <v-btn
                            x-large
                            class="px-16 mb-4"
                            elevation="0"
                            type="submit"
                            color="primary"
                            :loading="loading"
                            :disabled="loading"
                            @click="login"
                        >{{ $t("login") }}</v-btn>
                    </v-form>
                    <div
                        v-if="
                            generalSettings.social_login.facebook == 1 ||
                                generalSettings.social_login.twitter == 1 ||
                                generalSettings.social_login.google == 1
                        "
                    >
                        <div class="d-flex align-center mb-3">
                            <span class="me-2 fs-13 fw-500 opacity-60"
                            >{{ $t("or") }}, {{ $t("login_with") }}</span
                            >
                            <v-divider></v-divider>
                        </div>
                        <SocialLogin class="mb-5" />
                    </div>
                    <div>
                        {{ $t("dont_have_an_account") }},
                        <router-link
                            :to="{ name: 'Registration' }"
                            class="primary--text text-decoration-underline"
                        >{{ $t("signup") }}</router-link
                        >
                    </div>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { required, requiredIf, email } from "vuelidate/lib/validators";
import SocialLogin from "../auth/SocialLogin";
import { VueTelInput } from "vue-tel-input";
import { mapActions, mapGetters, mapMutations } from "vuex";
export default {
    data: () => ({
        mobileInputProps: {
            inputOptions: {
                type: "tel",
                placeholder: "phone number",
            },
            dropdownOptions: {
                showDialCodeInSelection: false,
                showFlags: true,
                showDialCodeInList: true,
            },
            autoDefaultCountry: false,
            validCharactersOnly: true,
            mode: "international",
        },
        showPhoneField: false,
        form: {
            phone: "",
            email: "",
            password: "",
            invalidPhone: true,
            showInvalidPhone: false,
        },
        passwordShow: false,
        loading: false,
    }),
    components: {
        SocialLogin,
        VueTelInput
    },
    validations: {
        form: {
            email: {
                requiredIf: requiredIf(function (){
                    return this.authSettings.customer_login_with == 'email' || (this.authSettings.customer_login_with == 'email_phone' && !this.showPhoneField)
                }),
                email,
            },
            phone: {
                requiredIf: requiredIf(function (){
                    return this.authSettings.customer_login_with == 'phone' || (this.authSettings.customer_login_with == 'email_phone' && this.showPhoneField)
                }),
            },
            password: {
                required,
            },
        },
    },
    computed: {
        ...mapGetters("app", ["generalSettings", "availableCountries", "demoMode", "banners"]),
        ...mapGetters("cart", ["getTempUserId"]),
        ...mapGetters("auth", ["authSettings"]),
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.requiredIf &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email &&
                errors.push(
                    this.$i18n.t("Este campo es requerido_a_valid_email")
                );
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
    },
    methods: {
        ...mapActions("auth", {
            actionLogin: "login",
        }),
        ...mapActions("app", ["fetchProductQuerries"]),
        ...mapActions("wishlist", ["fetchWislistProducts"]),
        ...mapActions("cart", ["fetchCartProducts"]),
        ...mapMutations("cart", ["removeTempUserId"]),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog"]),
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async login() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            if ((this.authSettings.customer_login_with == 'phone' || (this.authSettings.customer_login_with == 'email_phone' && this.showPhoneField)) && this.form.invalidPhone) {
                this.form.showInvalidPhone = true;
                return;
            }
            if (this.getTempUserId) {
                this.form.temp_user_id = this.getTempUserId;
            }
            this.form.phone = this.form.phone.replace(/\s/g, "");
            this.loading = true;
            const res = await this.call_api("post", "auth/login", this.form);
            if (res.data.success) {
                if (res.data.verified == true || this.authSettings.customer_otp_with == 'disabled') {
                    if (this.getTempUserId) {
                        this.removeTempUserId();
                    }
                    this.actionLogin(res.data);
                    this.showLoginDialog(false);
                    this.updateChatWindow(false);
                    this.fetchWislistProducts();
                    this.fetchProductQuerries();
                    this.fetchCartProducts();
                    this.$router.push(
                        this.$route.query.redirect || { name: "DashBoard" }
                    );
                }else{
                    if (
                        this.authSettings.customer_login_with == 'email' ||
                        (this.authSettings.customer_login_with == 'email_phone' && this.authSettings.customer_otp_with == 'email')
                    ) {
                        this.$router.push({
                            name: "VerifyAccount",
                            params: { email: this.form.email },
                        });
                    }else{
                        this.$router.push({
                            name: "VerifyAccount",
                            params: { phone: this.form.phone },
                        });
                    }
                }

                this.snack({
                    message: res.data.message,
                });
                
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red",
                });
            }
            this.loading = false;
        },
    },
    created() {
        if (this.demoMode) {
            this.form.email = "customer@example.com";
            this.form.password = "123456";
        }
    },
};
</script>
