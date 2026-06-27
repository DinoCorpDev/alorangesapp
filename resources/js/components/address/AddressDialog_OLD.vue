<template>
    <v-dialog v-model="isVisible" max-width="600px" @click:outside="closeDialog">
        <div class="white pa-5 rounded">
            <v-form lazy-validation v-on:submit.prevent="addNewAddress()" autocomplete="chrome-off">
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">{{ $t('address') }}</div>
                    <v-textarea
                        :placeholder="$t('address')"
                        v-model="form.address"
                        :error-messages="addressErrors"
                        hide-details="auto"
                        rows="3"
                        required
                        outlined
                        no-resize
                    ></v-textarea>
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">{{ $t('postal_code') }}</div>
                    <v-text-field
                        :placeholder="$t('postal_code')"
                        type="text"
                        v-model="form.postal_code"
                        :error-messages="postalCodeErrors"
                        hide-details="auto"
                        required
                        outlined
                    ></v-text-field>
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">{{ $t('country') }}</div>
                    <v-autocomplete
                        v-model="form.country"
                        :error-messages="countryErrors"
                        :items="countries"
                        :placeholder="$t('select_country')"
                        hide-details="auto"
                        outlined
                        item-text="name"
                        item-value="id"
                        dense
                        @input="countryChanged"
                    ></v-autocomplete>
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">{{ $t('state') }}</div>
                    <v-autocomplete
                        v-model="form.state"
                        :error-messages="stateErrors"
                        :items="filteredStates"
                        hide-details="auto"
                        :placeholder="statePlaceholer"
                        outlined
                        item-text="name"
                        item-value="id"
                        dense
                        @input="stateChanged"
                    ></v-autocomplete>
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">City</div>
                    <v-autocomplete
                        v-model="form.city"
                        :error-messages="cityErrors"
                        :items="filteredCities"
                        :placeholder="cityPlaceholer"
                        hide-details="auto"
                        outlined
                        item-text="name"
                        item-value="id"
                        dense
                    ></v-autocomplete>
                </div>
                <div class="mb-3">
                    <div class="mb-1 fs-13 fw-500">{{ $t('phone_number') }}</div>
                    <v-text-field
                        :placeholder="$t('phone_number')"
                        type="number"
                        v-model="form.phone"
                        :error-messages="phoneErrors"
                        hide-details="auto"
                        required
                        outlined
                    ></v-text-field>
                </div>
                <div class="text-right mt-4">
                    <v-btn text @click="closeDialog" >{{ $t('cancel') }}</v-btn>
                    <v-btn
                        v-if="!is_empty_obj(oldAddress)"
                        elevation="0"
                        type="submit"
                        color="primary"
                        @click="updateAddress"
                        :loading="adding"
                        :disabled="adding"
                    >{{ $t('update') }}</v-btn>
                    <v-btn
                        v-else
                        elevation="0"
                        type="submit"
                        color="primary"
                        @click="addNewAddress"
                        :loading="adding"
                        :disabled="adding"
                    >{{ $t('add_new') }}</v-btn>
                </div>
            </v-form>
        </div>
    </v-dialog>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import { mapMutations,mapActions } from "vuex";
export default {
    props: {
        show: { type: Boolean, required: true, default: false },
        oldAddress: { type: Object, default: () => {} }
    },
    data: () => ({
        adding: false,
        countriesLoaded: false,
        countries: [],
        filteredStates: [],
        filteredCities: [],
        form:{
            id: null,
            address: "",
            postal_code: "",
            country: "",
            state: "",
            city: "",
            phone: "",
        }
    }),
    validations: {
        form: {
            address: { required },
            postal_code: { required },
            country: { required },
            state: { required },
            city: { required },
            phone: { required },
        }
    },
    watch: {
        oldAddress(newVal, oldVal){
            if(newVal && !this.is_empty_obj(newVal)){
                this.processOldAddress(newVal)        
            }else{
                this.resetData()                
            }
        },
    },
    computed: {
        statePlaceholer(){
            return this.$i18n.t("select_a_state")
        },
        cityPlaceholer(){
            return this.$i18n.t("select_a_city")
        },
        isVisible: {
            get: function(){
                return this.show
            },
            set: function(newValue){}
        },
        addressErrors() {
            const errors = [];
            if (!this.$v.form.address.$dirty) return errors;
            !this.$v.form.address.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        postalCodeErrors() {
            const errors = [];
            if (!this.$v.form.postal_code.$dirty) return errors;
            !this.$v.form.postal_code.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.form.country.$dirty) return errors;
            !this.$v.form.country.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.form.state.$dirty) return errors;
            !this.$v.form.state.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.form.city.$dirty) return errors;
            !this.$v.form.city.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        phoneErrors() {
            const errors = [];
            if (!this.$v.form.phone.$dirty) return errors;
            !this.$v.form.phone.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
    },
    created(){
        this.fetchCountries();
    },
    methods: {
        ...mapActions("address",[
            "addAddress",
        ]),
        ...mapMutations("address",[
            "setAddresses"
        ]),
        async fetchCountries(){
            if(!this.countriesLoaded){
                const res = await this.call_api("get", "all-countries");
                if(res.data.success){
                    this.countriesLoaded = true
                    this.countries = res.data.data
                }
            }
        },
        async countryChanged(countryid){
            const res = await this.call_api("get", `states/${countryid}`);
            if(res.data.success){
                this.filteredStates = res.data.data
                this.form.state = ""
                this.form.city = ""
                this.filteredCities = []
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: 'red'
                });
            }
        },
        async stateChanged(stateid){
            const res = await this.call_api("get", `cities/${stateid}`);
            if(res.data.success){
                this.filteredCities = res.data.data
                this.form.city = ""
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: 'red'
                });
            }
        },
        async addNewAddress(){
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.adding = true;
            const res = await this.call_api("post", "user/address/create",this.form);
            if(res.data.success){
                this.addAddress(res.data.data)
                this.snack({ message: res.data.message });
                this.resetData();
                this.closeDialog();                
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.adding = false;
        },
        async updateAddress(){
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.adding = true;
            const res = await this.call_api("post", `user/address/update`,this.form);
            if(res.data.success){
                this.setAddresses(res.data.data)
                this.snack({ message: res.data.message });
                this.closeDialog();                
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.adding = false;
        },
        resetData(){
            this.form.id = null;
            this.form.address = "";
            this.form.postal_code = "";
            this.form.country = "";
            this.form.state = "";
            this.form.city = "";
            this.form.phone = "";

            this.$v.form.$reset();
        },
        async processOldAddress(oldVal){
            let oldAddress = { ...oldVal }

            this.form.id = oldAddress.id;
            this.form.address = oldAddress.address;
            this.form.postal_code = oldAddress.postal_code;
            this.form.phone = oldAddress.phone;

            //find selected country and filter states
            let selectedCountry = this.countries.find(country => country.name === oldAddress.country)
            this.form.country = selectedCountry.id;
            await this.countryChanged(selectedCountry.id)

            //find selected state and filter cities
            let selectedState = this.filteredStates.find(state => state.name === oldAddress.state)
            this.form.state = selectedState.id;
            await this.stateChanged(selectedState.id)

            //find selected city
            let selectedCity = this.filteredCities.find(city => city.name === oldAddress.city)
            this.form.city = selectedCity.id;

        },
        closeDialog(){
            this.isVisible = false
            this.$emit('close')
        }
    }
}
</script>