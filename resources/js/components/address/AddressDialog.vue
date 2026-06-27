<template>
    <div v-if="isVisible">
        <div class="white pa-5 rounded">
            <v-form :validator="$v.form" autocomplete="chrome-off">
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">Nombre dirección</div>
                    <CustomInput
                        v-model="form.name"
                        :error-messages="addressNameErrors"
                        @blur="$v.form.name.$touch()"
                        required
                        placeholder="Nombre de la dirección"
                    />
                </div>
                
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500"> Dirección</div>
                   <CustomInput
                        v-model="form.address"
                        :error-messages="addressErrors"
                        @blur="$v.form.address.$touch()"
                        placeholder="Nombre"
                        required
                    />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">Código postal</div>
                    <CustomInput
                        class="select-placeholder"
                        v-model="form.postal_code"
                        :error-messages="postalCodeErrors"
                        @blur="$v.form.postal_code.$touch()"
                    />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500"> País </div>
                    <SelectCustom
                        :error-messages="countryErrors"
                        placeholder="País"
                        :items="countries"
                        @blur="$v.form.country.$touch()"
                        @input="countryChanged"
                        item-text="name"
                        item-value="id"
                        required
                        v-model="form.country"
                    />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500"> Estado / Departamento </div>
                    <SelectCustom
                        :error-messages="stateErrors"
                        placeholder="Estado o departamento"
                        :items="filteredStates"
                        @blur="$v.form.state.$touch()"
                        @input="stateChanged"
                        item-text="name"
                        item-value="id"
                        required
                        v-model="form.state"
                    />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">Ciudad</div>
                    <SelectCustom
                        placeholder="Ciudad"
                        :error-messages="cityErrors"
                        :items="filteredCities"
                        @blur="$v.form.city.$touch()"
                        item-text="name"
                        item-value="id"
                        required
                        v-model="form.city"
                    />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">Barrio ( Opcional )</div>
                    <CustomInput v-model="form.neighborhood" placeholder="Barrio" />
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500"> Teléfono </div>
                    <v-row>
                        <v-col cols="12">
                            <vue-tel-input
                                v-model="form.phone"
                                placeholder="Ingresar teléfono / celular"
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
                </div>
                <div class="text-right mt-4">
                    <CustomButton text="Cancelar" color="white" @click="closeDialog" />
                    <CustomButton
                        v-if="!is_empty_obj(oldAddress)"
                        elevation="0"
                        text="Actualizar"
                        color="orange"
                        @click.stop="updateAddress"
                        :loading="adding"
                        :disabled="adding" />
                    <CustomButton
                        v-else
                        text="Agregar"
                        elevation="0"
                        color="orange"
                        @click.stop="addNewAddress"
                        :loading="adding"
                        :disabled="adding"
                        />
                </div>
            </v-form>
        </div>
    </div>
</template>

<script>
import { VueTelInput } from "vue-tel-input";
import { required } from "vuelidate/lib/validators";
import { mapMutations, mapGetters, mapActions } from "vuex";
import CustomInput from "../../components/global/CustomInput.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import snackbar from "../../components/inc/SnackBar";
import CustomButton from "../../components/global/CustomButton.vue";

export default {
    props: {
        show: { type: Boolean, required: true, default: false },
        oldAddress: { type: Object, default: () => {} },
        typeAddress: { type: String, default: "shipping" }
    },
    components: {
        SelectCustom,
        CustomInput,
        CustomButton,
        VueTelInput,
        snackbar
    },
    data: () => ({
        codigoPostalTypes: [],
        adding: false,
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
        form: {
            id: null,
            address: "",
            name: "",
            details: "",
            postal_code: "",
            country: "",
            neighborhood: "",
            state: "",
            city: "",
            phone: "",
            invalidPhone: true,
            showInvalidPhone: false
        },
    }),
    validations: {
        form: {
            address: { required },
            name: { required },
            country: { required },
            state: { required },
            city: { required },
            phone: { required },
        }
    },
    watch: {
        oldAddress(newVal, oldVal) {
            if (newVal && !this.is_empty_obj(newVal)) {
                this.processOldAddress(newVal);
            } else {
                this.resetData();
            }
        }
    },
    computed: {
        ...mapGetters("app", ["availableCountries"]),
        statePlaceholer() {
            return this.$i18n.t("select_a_state");
        },
        cityPlaceholer() {
            return this.$i18n.t("select_a_city");
        },
        isVisible: {
            get: function () {
                return this.show;
            },
            set: function (newValue) {}
        },
        addressErrors() {
            const errors = [];
            if (!this.$v.form.address.$dirty){ return errors; }
            !this.$v.form.address.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressNameErrors() {
            const errors = [];
            if (!this.$v.form.name.$dirty){ return errors; }
            !this.$v.form.name.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressDetailsErrors() {
            const errors = [];
            if (!this.$v.form.details.$dirty){ return errors; }
            !this.$v.form.details.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.form.country.$dirty){ return errors; }
            !this.$v.form.country.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.form.state.$dirty){ return errors; }
            !this.$v.form.state.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.form.city.$dirty){ return errors; }
            !this.$v.form.city.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        phoneErrors() {
            const errors = [];
            if (!this.$v.form.phone.$dirty){ return errors; }
            !this.$v.form.phone.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        }
    },
    methods: {
        ...mapActions("address", ["addAddress"]),
        ...mapMutations("address", ["setAddresses"]),
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        async fetchCodigoPostal() {
            const res = await this.call_api("get", "all-codigo-postal");
            if (res.data.success) {
                this.codigoPostalTypes = res.data.data;
            }
        },
        async addNewAddress() {
            this.$v.form.$touch();
        
            if (this.$v.form.$anyError) {
                return;
            }

            this.form.phone = this.form.phone.replace(/\s/g, "");

            this.adding = true;
            let data = {
                type: this.typeAddress,
                ...this.form
            };
            const res = await this.call_api("post", "user/address/create", data);
            if (res.data.success) {
                this.addAddress(res.data.data);
                this.snack({ message: res.data.message });
                this.resetData();
                this.closeDialog();
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.adding = false;
            const currentScroll = window.scrollY;
            window.scrollTo(0, currentScroll);
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
        async updateAddress() {
            this.$v.form.$touch();
            
            if (this.$v.form.$anyError) {
                return; 
            } 

            this.adding = true;

            const res = await this.call_api("post", `user/address/update`, this.form);
            if (res.data.success) {
                this.setAddresses(res.data.data);
                this.snack({ message: res.data.message });
                this.closeDialog();
                this.resetData();
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.adding = false;
        },
        resetData() {
            this.form.id = null;
            this.form.address = "";
            this.form.name = "";
            this.form.neighborhood = "";
            this.form.postal_code = "";
            this.form.country = "";
            this.form.state = "";
            this.form.city = "";
            this.form.phone = "";

            this.$v.form.$reset();
        },
        async processOldAddress(oldVal) {
            let oldAddress = { ...oldVal };

            this.form.id = oldAddress.id;
            this.form.address = oldAddress.address;
            this.form.name = oldAddress.name;
            this.form.neighborhood = oldAddress.neighborhood;
            this.form.postal_code = oldAddress.postal_code;
            this.form.phone = oldAddress.phone;
            this.form.country = oldAddress.country_id;

            let country = await this.countryChanged(oldAddress.country_id);
            this.form.state = oldAddress.state_id;

            let state = await this.stateChanged(oldAddress.state_id);
            this.form.city = oldAddress.city_id;
        },
        closeDialog() {
            this.isVisible = false;
            this.$emit("close");
        }
    },
    created() {
        this.resetData();
        this.fetchCountries();
        this.fetchCodigoPostal();
    },
};
</script>
