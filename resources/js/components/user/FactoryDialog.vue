<template>
    <v-dialog v-model="isVisible" max-width="960px" @click:outside="closeDialog">
        <div class="white pa-8 rounded">
            <v-form class="modal-empresa-form" ref="empresaForm"  lazy-validation>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Razón social de la empresa</span>
                        <custom-input
                            v-model="form.companyRazon"
                            @blur="$v.form.companyRazon.$touch()"
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Entidad Comercial</span>
                        <select-custom
                            :items="companyTypes"
                            v-model="form.companyType"
                         
                            @blur="$v.form.companyType.$touch()"
                            required
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">Documento</span>
                        <select-custom
                            :items="documentTypes"
                            v-model="form.companyDocumentType"
                       
                            @blur="$v.form.companyDocumentType.$touch()"
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <span class="black--text body-2 text-uppercase">
                            Numero de
                            {{ form.companyDocumentType ? form.companyDocumentType : "Documento" }}
                        </span>
                        <custom-input
                            v-model="form.companyDocumentNumber"
                       
                            @blur="$v.form.companyDocumentNumber.$touch()"
                            required
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6"> </v-col>
                    <v-col cols="12" md="6">
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
import { mapActions, mapGetters, mapMutations } from "vuex";
import { required, requiredIf, email, minLength, sameAs } from "vuelidate/lib/validators";
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
        mobileInputProps: {
            inputOptions: {
                type: "tel",
                placeholder: "--"
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
            personType: "Natural",
            firstName: "",
            secondName: "",
            firstLastname: "",
            secondLastname: "",
            documentType: "",
            documentNumber: "",
            companyRazon: "",
            companyType: "",
            companyDocumentType: "",
            companyDocumentNumber: "",
            phone: "",
            oldPassword: "",
            invalidPhone: true,
            showInvalidPhone: false
        },
        infoUpdateLoading: false
    }),
    validations: {
        form: {
            companyRazon: { required },
            companyType: { required },
            companyDocumentType: { required },
            companyDocumentNumber: { required },
        }
    },
    async created() {
        this.form.companyRazon = this.currentUser.company_razon;
        this.form.companyType = this.currentUser.company_type;
        this.form.companyDocumentType = this.currentUser.company_document_type;
        this.form.companyDocumentNumber = this.currentUser.company_document_number;
    },
    watch: {
        oldFactory(newVal, oldVal) {
            if (newVal && !this.is_empty_obj(newVal)) {
                this.processOldProfile(newVal);
            } else {
                this.resetData();
            }
        }
    },
    computed: {
        ...mapGetters("auth", ["currentUser", "getUser"]),
        ...mapGetters("app", ["generalSettings", "availableCountries"]),
        isVisible: {
            get: function () {
                return this.show;
            },
            set: function (newValue) {}
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
        companyRazonErrors() {
            const errors = [];
            if (!this.$v.form.companyRazon.$dirty) return errors;
            !this.$v.form.companyRazon.requiredIf && errors.push(this.$i18n.t("Este campo es requerido"));
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

            if (this.$v.form.$anyError) {
                return;
            }

            this.infoUpdateLoading = true;

            const res = await this.call_api("post", "user/info/updateEmpresa", this.form);

            if (res.data.success) {
                this.setUser(res.data.user);
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }

            this.infoUpdateLoading = false;
            this.closeDialog();
        },
        async processOldProfile(oldVal) {
            let oldProfile = { ...oldVal };            
        },
        closeDialog() {
            this.isVisible = false;
            this.$emit("close");
        }
    }
};
</script>
