<template>
    <div class="register d-flex flex-column h-100">
        <v-container class="flex-grow-1 mb-5" fluid>
            <v-row justify="center">
                <v-col cols="12" sm="11" md="10" lg="8">
                    <v-row class="wrap pa-5" no-gutters>
                        <v-col cols="12">
                            <div class="register-content h-100 pa-lg-5 pt-lg-8">
                                <h1 class="register-title">Registro</h1>
                                <v-divider class="my-4" />
                                <v-form
                                    class="inputs mb-8"
                                    ref="loginForm"
                                    lazy-validation
                                    @submit.prevent="register()"
                                >
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                {{ $t("email_address") }}
                                            </span>
                                            <CustomInput
                                                type="email"
                                                v-model="form.email"
                                                :error-messages="emailErrors"
                                                @blur="$v.form.email.$touch()"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                {{ $t("password") }}
                                            </span>
                                            <CustomInput
                                                v-model="form.password"
                                                :error-messages="passwordErrors"
                                                @blur="$v.form.password.$touch()"
                                                type="password"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                {{ $t("confirm_password") }}
                                            </span>
                                            <CustomInput
                                                v-model="form.confirmPassword"
                                                :error-messages="confirmPasswordErrors"
                                                @blur="$v.form.confirmPassword.$touch()"
                                                type="password"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" sm="6">
                                            <CustomCheckbox
                                                inputValue="Natural"
                                                label="Registrar como persona natural"
                                                name="personType"
                                                type="radio"
                                                v-model="form.personType"
                                            />
                                        </v-col>
                                        <v-col cols="12" sm="6">
                                            <CustomCheckbox
                                                inputValue="Juridical"
                                                label="Registrar como persona jurídica"
                                                name="personType"
                                                type="radio"
                                                v-model="form.personType"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-row>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">Primer Nombre</span>
                                                    <CustomInput
                                                        v-model="form.firstName"
                                                        :error-messages="firstNameErrors"
                                                        @blur="$v.form.firstName.$touch()"
                                                        required
                                                    />
                                                </v-col>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">
                                                        Segundo Nombre (Opcional)
                                                    </span>
                                                    <CustomInput v-model="form.secondName" />
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-row>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">
                                                        Primer Apellido
                                                    </span>
                                                    <CustomInput
                                                        v-model="form.firstLastname"
                                                        :error-messages="firstLastnameErrors"
                                                        @blur="$v.form.firstLastname.$touch()"
                                                        required
                                                    />
                                                </v-col>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">
                                                        Segundo Apellido
                                                    </span>
                                                    <CustomInput
                                                        v-model="form.secondLastname"
                                                        :error-messages="secondLastnameErrors"
                                                        @blur="$v.form.secondLastname.$touch()"
                                                        required
                                                    />
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                Documento (Representante)
                                            </span>
                                            <SelectCustom
                                                :items="documentTypes"
                                                v-model="form.documentType"
                                                :error-messages="documentTypeErrors"
                                                @blur="$v.form.documentType.$touch()"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Numero de Documento</span>
                                            <CustomInput
                                                v-model="form.documentNumber"
                                                :error-messages="documentNumberErrors"
                                                @blur="$v.form.documentNumber.$touch()"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row v-if="form.personType == 'Juridical'">
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Nombre de la Empresa</span>
                                            <CustomInput
                                                v-model="form.companyName"
                                                :error-messages="companyNameErrors"
                                                @blur="$v.form.companyName.$touch()"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Entidad Comercial</span>
                                            <SelectCustom
                                                :items="companyTypes"
                                                v-model="form.companyType"
                                                :error-messages="companyTypeErrors"
                                                @blur="$v.form.companyType.$touch()"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row v-if="form.personType == 'Juridical'">
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Documento</span>
                                            <SelectCustom
                                                :items="documentTypes"
                                                v-model="form.companyDocumentType"
                                                :error-messages="companyDocumentTypeErrors"
                                                @blur="$v.form.companyDocumentType.$touch()"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                Numero de
                                                {{ form.companyDocumentType ? form.companyDocumentType : "Documento" }}
                                            </span>
                                            <CustomInput
                                                v-model="form.companyDocumentNumber"
                                                :error-messages="companyDocumentNumberErrors"
                                                @blur="$v.form.companyDocumentNumber.$touch()"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row v-if="form.personType == 'Juridical'">
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                Documento (Representante)
                                            </span>
                                            <CustomButton block class="mt-3" color="nero" text="Añadir Mi Empresa" />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                Documento (Representante)
                                            </span>
                                            <CustomButton block class="mt-3" color="nero" text="Añadir Mi Empresa" />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-row>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">
                                                        Nombre De Dirección (Casa / Oficina)
                                                    </span>
                                                    <CustomInput
                                                        v-model="mainAddress.name"
                                                        :error-messages="addressNameErrors"
                                                        @blur="$v.mainAddress.name.$touch()"
                                                        required
                                                    />
                                                </v-col>
                                                <v-col cols="12" sm="6">
                                                    <span class="black--text body-2 text-uppercase">
                                                        Dirección (Calle / Carrera)
                                                    </span>
                                                    <CustomInput
                                                        v-model="mainAddress.address"
                                                        :error-messages="addressErrors"
                                                        @blur="$v.mainAddress.address.$touch()"
                                                        required
                                                    />
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">
                                                Dirección Adicional (Piso / Apartamento / Oficina)
                                            </span>
                                            <CustomInput
                                                v-model="mainAddress.details"
                                                :error-messages="addressDetailsErrors"
                                                @blur="$v.mainAddress.details.$touch()"
                                                required
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Codigo Postal</span>
                                            <CustomInput
                                                v-model="mainAddress.postal_code"
                                                :error-messages="postalCodeErros"
                                                @blur="$v.mainAddress.postal_code.$touch()"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Departamento</span>
                                            <SelectCustom
                                                :error-messages="stateErrors"
                                                :items="filteredStates"
                                                @blur="$v.mainAddress.state.$touch()"
                                                @input="stateChanged"
                                                item-text="name"
                                                item-value="id"
                                                required
                                                v-model="mainAddress.state"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Municipio</span>
                                            <SelectCustom
                                                :error-messages="cityErrors"
                                                :items="filteredCities"
                                                @blur="$v.mainAddress.city.$touch()"
                                                item-text="name"
                                                item-value="id"
                                                required
                                                v-model="mainAddress.city"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Barrio ( Opcional )</span>
                                            <CustomInput v-model="mainAddress.neighborhood" />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Pais</span>
                                            <SelectCustom
                                                :error-messages="countryErrors"
                                                :items="countries"
                                                @blur="$v.mainAddress.country.$touch()"
                                                @input="countryChanged"
                                                item-text="name"
                                                item-value="id"
                                                required
                                                v-model="mainAddress.country"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <span class="black--text body-2 text-uppercase">Teléfono / Mobil</span>
                                            <v-row>
                                                <v-col cols="12">
                                                    <vue-tel-input
                                                        v-model="form.phone"
                                                        v-bind="mobileInputProps"
                                                        :onlyCountries="availableCountries"
                                                        @validate="phoneValidate"
                                                        @blur="$v.form.phone.$touch()"
                                                        :class="{
                                                            'error--text': $v.form.phone.$error || form.showInvalidPhone
                                                        }"
                                                    >
                                                        <template slot="arrow-icon">
                                                            <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                        </template>
                                                    </vue-tel-input>
                                                    <div
                                                        class="v-text-field__details mt-2 pl-3"
                                                        v-if="$v.form.phone.$error"
                                                    >
                                                        <div class="v-messages theme--light error--text" role="alert">
                                                            <div class="v-messages__wrapper">
                                                                <div class="v-messages__message">
                                                                    {{ $t("Este campo es requerido") }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="v-text-field__details mt-2 pl-3"
                                                        v-if="!$v.form.phone.$error && form.showInvalidPhone"
                                                    >
                                                        <div class="v-messages theme--light error--text" role="alert">
                                                            <div class="v-messages__wrapper">
                                                                <div class="v-messages__message">
                                                                    {{ $t("phone_number_must_be_valid") }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <CustomCheckbox
                                                class="mb-3"
                                                label="He podido leer y entiendo la política de privacidad y cookies."
                                                :error="$v.form.policiesAndCookiesConsent.$error"
                                                @change="$v.form.policiesAndCookiesConsent.$touch()"
                                                v-model="form.policiesAndCookiesConsent"
                                                required
                                            />
                                            <CustomCheckbox
                                                label="Quiero recibir comunicaciones comerciales personalizadas de Aloranges a través de email."
                                                v-model="form.offersConsent"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <CustomButton
                                                block
                                                class="mt-3"
                                                text="Guardar"
                                                type="submit"
                                                color="black"
                                                @click="register"
                                                :disabled="loading"
                                                :loading="loading"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-form>
                                <AuthFooter />
                            </div>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import { VueTelInput } from "vue-tel-input";
import { mapActions, mapGetters, mapMutations } from "vuex";
import { required, requiredIf, email, minLength, sameAs } from "vuelidate/lib/validators";

import AuthFooter from "./AuthFooter.vue";
import CarouselDescription from "../../components/global/CarouselDescription.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomCheckbox from "../../components/global/CustomCheckbox.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import snackbar from "../../components/inc/SnackBar";

// Custom validators
const isTrue = value => value === true;

export default {
    components: {
        AuthFooter,
        CarouselDescription,
        CustomButton,
        CustomCheckbox,
        CustomInput,
        SelectCustom,
        VueTelInput,
        snackbar
    },
    data() {
        return {
            mobileInputProps: {
                inputOptions: {
                    type: "tel",
                    placeholder: "--"
                },
                dropdownOptions: {
                    showDialCodeInSelection: false,
                    showFlags: true,
                    showDialCodeInList: true,
                    showSearchBox: true
                },
                autoDefaultCountry: false,
                validCharactersOnly: true,
                mode: "international"
            },
            countriesLoaded: false,
            countries: [],
            filteredStates: [],
            filteredCities: [],
            documentTypes: [
                { text: "(C.C) Cedula de ciudadanía", value: "C.C" },
                { text: "(N.I.T) Numero de identificación tributario", value: "N.I.T" }
            ],
            companyTypes: [{ text: "(S.A.S) Sociedad por acciones simplificadas", value: "S.A.S" }],
            form: {
                email: "",
                password: "",
                confirmPassword: "",
                personType: "Natural",
                firstName: "",
                secondName: "",
                firstLastname: "",
                secondLastname: "",
                documentType: "",
                documentNumber: "",
                companyName: "",
                companyType: "",
                companyDocumentType: "",
                companyDocumentNumber: "",
                phone: "",
                policiesAndCookiesConsent: false,
                offersConsent: false,
                invalidPhone: true,
                showInvalidPhone: false
            },
            mainAddress: {
                customer_id: null,
                address: "",
                name: "",
                details: "",
                country: "",
                state: "",
                city: "",
                neighborhood: "",
                postal_code: "",
                phone: "",
                type: "shipping"
            },
            loading: false
        };
    },
    validations: {
        form: {
            email: { required, email },
            password: { required, minLength: minLength(6) },
            confirmPassword: { required, sameAsPassword: sameAs("password") },
            personType: { required },
            firstName: { required },
            secondName: { required },
            firstLastname: { required },
            secondLastname: { required },
            documentType: { required },
            documentNumber: { required },
            companyName: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyType: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyDocumentType: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyDocumentNumber: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            phone: { required },
            policiesAndCookiesConsent: { required, isTrue }
        },
        mainAddress: {
            address: { required },
            name: { required },
            details: { required },
            country: { required },
            state: { required },
            city: { required },
            postal_code: { required }
        }
    },
    computed: {
        ...mapGetters("app", ["generalSettings", "availableCountries"]),
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
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
        personTypeErrors() {
            const errors = [];
            if (!this.$v.form.personType.$dirty) return errors;
            !this.$v.form.personType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        firstNameErrors() {
            const errors = [];
            if (!this.$v.form.firstName.$dirty) return errors;
            !this.$v.form.firstName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        secondNameErrors() {
            const errors = [];
            if (!this.$v.form.secondName.$dirty) return errors;
            !this.$v.form.secondName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        firstLastnameErrors() {
            const errors = [];
            if (!this.$v.form.firstLastname.$dirty) return errors;
            !this.$v.form.firstLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        secondLastnameErrors() {
            const errors = [];
            if (!this.$v.form.secondLastname.$dirty) return errors;
            !this.$v.form.secondLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        documentTypeErrors() {
            const errors = [];
            if (!this.$v.form.documentType.$dirty) return errors;
            !this.$v.form.documentType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        documentNumberErrors() {
            const errors = [];
            if (!this.$v.form.documentNumber.$dirty) return errors;
            !this.$v.form.documentNumber.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyNameErrors() {
            const errors = [];
            if (!this.$v.form.companyName.$dirty) return errors;
            !this.$v.form.companyName.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyTypeErrors() {
            const errors = [];
            if (!this.$v.form.companyType.$dirty) return errors;
            !this.$v.form.companyType.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.form.companyDocumentType.$dirty) return errors;
            !this.$v.form.companyDocumentType.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.form.companyDocumentNumber.$dirty) return errors;
            !this.$v.form.companyDocumentNumber.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressErrors() {
            const errors = [];
            if (!this.$v.mainAddress.address.$dirty) return errors;
            !this.$v.mainAddress.address.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressNameErrors() {
            const errors = [];
            if (!this.$v.mainAddress.name.$dirty) return errors;
            !this.$v.mainAddress.name.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressDetailsErrors() {
            const errors = [];
            if (!this.$v.mainAddress.details.$dirty) return errors;
            !this.$v.mainAddress.details.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.mainAddress.country.$dirty) return errors;
            !this.$v.mainAddress.country.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.mainAddress.state.$dirty) return errors;
            !this.$v.mainAddress.state.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.mainAddress.city.$dirty) return errors;
            !this.$v.mainAddress.city.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        postalCodeErros() {
            const errors = [];
            if (!this.$v.mainAddress.postal_code.$dirty) return errors;
            !this.$v.mainAddress.postal_code.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        }
    },
    created() {
        this.fetchCountries();
    },
    methods: {
        ...mapActions("auth", ["login"]),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog"]),
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async register() {
            this.$v.form.$touch();
            this.$v.mainAddress.$touch();

            if (this.form.invalidPhone) {
                this.form.showInvalidPhone = true;
                return;
            }

            if (this.$v.form.$anyError || this.$v.mainAddress.$anyError) {
                return;
            }

            this.form.phone = this.form.phone.replace(/\s/g, "");
            this.mainAddress.phone = this.form.phone;

            this.loading = true;

            const res = await this.call_api("post", "auth/signup", this.form);

            if (res.data.success) {
                this.mainAddress.customer_id = res.data.user.id;

                await this.saveAddress().then(() => {
                    this.login(res.data);
                    this.showLoginDialog(false);
                    this.updateChatWindow(false);
                    this.$router.push(this.$route.query.redirect || { name: "RegistrationSuccess" });
                });
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }

            this.loading = false;
        },
        async fetchCountries() {
            if (!this.countriesLoaded) {
                const res = await this.call_api("get", "all-countries");
                if (res.data.success) {
                    this.countriesLoaded = true;
                    this.countries = res.data.data;
                }
            }
        },
        async countryChanged(countryid) {
            const res = await this.call_api("get", `states/${countryid}`);
            if (res.data.success) {
                this.filteredStates = res.data.data;
                this.form.state = "";
                this.form.city = "";
                this.filteredCities = [];
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async stateChanged(stateid) {
            const res = await this.call_api("get", `cities/${stateid}`);
            if (res.data.success) {
                this.filteredCities = res.data.data;
                this.form.city = "";
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async saveAddress() {
            this.mainAddress.phone = this.form.phone;

            try {
                const res = await this.call_api("post", "user/address/create", this.mainAddress);

                if (res.data.success) {
                    return res.data;
                } else {
                    this.snack({
                        message: res.data.message,
                        color: "red"
                    });
                }
            } catch (error) {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
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
.register {
    &-title {
        font-size: 24px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 30px;
    }

    &-content {
        @media (min-width: 1264px) {
            border: 1px solid #e4e4e4;
            border-radius: 10px;
        }

        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}

.wrap {
    background-color: #fafcfc;
    border-radius: 10px;
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
</style>
