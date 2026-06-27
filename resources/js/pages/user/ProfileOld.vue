<template>
    <div class="ps-lg-7 pt-4">
        <h1 class="fs-21 fw-700 opacity-80 mb-5">{{ $t("profile") }}</h1>
        <v-row>
            <v-col xl="8">
                <v-card elevation="0" class="mb-6">
                    <v-card-title class="pa-0 fs-16 fw-700">{{ $t("profile_information") }}</v-card-title>
                    <v-form class="py-4" lazy-validation v-on:submit.prevent="updateBasic()" enctype="multipart/form-data">
                        <input type="email" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                        <input type="password" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />

                        <label>{{ $t("full_name") }}</label>
                        <v-text-field :placeholder="$t('full_name')" v-model="form.name" :error-messages="nameErrors" outlined hide-details="auto" class="mb-3"></v-text-field>

                        <label>{{ $t("email") }}</label>
                        <v-text-field :placeholder="$t('email_address')" v-model="form.email" outlined hide-details="auto" class="mb-3" disabled></v-text-field>

                        <label>{{ $t("phone_number") }}</label>
                        <vue-tel-input v-model="form.phone" v-bind="mobileInputProps" :onlyCountries="availableCountries" class="mb-3 opacity-80" @validate="phoneValidate" disabled></vue-tel-input>

                        <!-- <label>{{ $t("old_password") }}</label>
                        <v-text-field placeholder="******" type="password" v-model="form.oldPassword" :error-messages="oldPasswordErrors" outlined hide-details="auto" class="mb-3"></v-text-field> -->

                        <label>{{ $t("new_password") }}</label>
                        <v-text-field placeholder="******" type="password" v-model="form.password" :error-messages="passwordErrors" @blur="$v.form.password.$touch()" outlined hide-details="auto" class="mb-3"></v-text-field>

                        <label>{{ $t("confirm_password") }}</label>
                        <v-text-field placeholder="******" type="password" v-model="form.confirmPassword" :error-messages="confirmPasswordErrors" @blur="$v.form.confirmPassword.$touch()" outlined hide-details="auto" class="mb-3"></v-text-field>

                        <label>{{ $t("profile_image") }}</label>
                        <div class="d-flex flex-column align-center justify-center border rounded pa-6 mb-3">
                            <div class="avatar-upload">
                                <v-avatar size="160" class="">
                                    <img :src="form.previewAvatar" @error="imageFallback($event)">
                                </v-avatar>
                                <label>
                                    <input type="file" id="avatar-input" accept="image/png, image/jpg, image/jpeg" @change="previewThumbnail">
                                    <i class="las la-image"></i>
                                </label>
                            </div>
                            <label for="avatar-input" class="border border-gray-300 fs-14 fw-500 grey lighten-4 mt-3 opacity-50 px-4 py-1 rounded c-pointer">{{ $t("select_image") }}</label>
                        </div>
                        <v-btn type="submit" :loading="infoUpdateLoading" :disabled="infoUpdateLoading" color="primary" elevation="0" class="px-10" @click="updateBasic">{{ $t("update") }}</v-btn>
                    </v-form>
                </v-card>
                <v-card elevation="0">
                    <address-dialog :show="addDialogShow" @close="addressDialogClosed" :old-address="addressSelectedForEdit" />
                    <v-card-title class="pa-0 fs-16 fw-700 mb-4">
                        <span class="">{{ $t("addresses") }}</span>
                        <v-btn color="primary" elevation="0"  class="ms-auto" @click.stop="addDialogShow = true">{{ $t("add_new") }}</v-btn>
                    </v-card-title>
                    <v-row class="mb-4 row-cols-1 row-cols-sm-2 gutters-10">
                        <v-col>
                            <div class="fs-13 fw-600 opacity-80 mb-2">{{ $t("default_shipping_address") }}</div>
                            <div class="border rounded pa-4 fs-13" v-if="getDefaultShippingAddress">
                                <div>{{ getDefaultShippingAddress.address }}</div>
                                <div>{{ getDefaultShippingAddress.postal_code }}, {{ getDefaultShippingAddress.city }}, {{ getDefaultShippingAddress.state }}</div>
                                <div>{{ getDefaultShippingAddress.country }}</div>
                                <div>{{ getDefaultShippingAddress.phone }}</div>
                            </div>
                        </v-col>
                        <v-col>
                            <div class="fs-13 fw-600 opacity-80 mb-2">{{ $t("default_billing_address") }}</div>
                            <div class="border rounded pa-4 fs-13" v-if="getDefaultBillingAddress">
                                <div>{{ getDefaultBillingAddress.address }}</div>
                                <div>{{ getDefaultBillingAddress.postal_code }}, {{ getDefaultBillingAddress.city }}, {{ getDefaultBillingAddress.state }}</div>
                                <div>{{ getDefaultBillingAddress.country }}</div>
                                <div>{{ getDefaultBillingAddress.phone }}</div>
                            </div>
                        </v-col>
                    </v-row>
                </v-card>
                <v-card elevation="0">
                    <v-card-title class="pa-0 fs-16 fw-700 mb-4">{{ $t("all_addresses") }}</v-card-title>
                    <div>
                        <v-row class="row-cols-1 row-cols-sm-2 gutters-10" v-if="addressesLoaded && getAddresses.length > 0">
                            <v-col v-for="(address, i) in getAddresses" :key="i">
                                <div class="border rounded py-4 ps-4 pe-9 fs-13 position-relative">
                                    <div>{{ address.address }}</div>
                                    <div>{{ address.postal_code }}, {{ address.city }}, {{ address.state }}</div>
                                    <div>{{ address.country }}</div>
                                    <div>{{ address.phone }}</div>
                                    <v-menu offset-y left>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                class="absolute-top-right"
                                                color="transparent"
                                                elevation="0"
                                                v-bind="attrs"
                                                v-on="on"
                                                fab
                                                small
                                            >
                                                <i class="las la-ellipsis-v fs-18"></i>
                                            </v-btn>
                                        </template>
                                        <v-list class="">
                                            <v-list-item class="c-pointer" @click="editAddress(address)">
                                                <v-list-item-title>{{ $t("edit") }}</v-list-item-title>
                                                </v-list-item>
                                            <v-list-item class="c-pointer" @click="markDefaultShipping(address.id)">
                                                <v-list-item-title>{{ $t("mark_as_default_shipping_address") }}</v-list-item-title>
                                                </v-list-item>
                                            <v-list-item class="c-pointer" @click="markDefaultBilling(address.id)">
                                                <v-list-item-title>{{ $t("mark_as_default_billing_address") }}</v-list-item-title>
                                                </v-list-item>
                                            <v-list-item class="c-pointer" @click="deleteAddress(address.id)">
                                                <v-list-item-title>{{ $t("delete") }}</v-list-item-title>
                                                </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </div>
                            </v-col>
                        </v-row>
                        <div class="text-center" v-else>
                            <div>{{ $t("no_saved_address") }}</div>
                        </div>
                    </div>
                </v-card>
            </v-col>
            <v-col cols="auto">
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from "vuex";
import { required, email, minLength, sameAs, requiredIf } from "vuelidate/lib/validators";
import { VueTelInput } from 'vue-tel-input'
import AddressDialog from '../../components/address/AddressDialog.vue';
export default {
    data: () => ({
        mobileInputProps:{
            inputOptions: {
                type: 'tel',
                placeholder: 'phone number',
            },
            dropdownOptions:{
                showDialCodeInSelection: false,
                showFlags: true,
                showDialCodeInList: true
            },
            autoDefaultCountry: false,
            validCharactersOnly: true,
            mode: 'international',
        },
        form: {
            name: "",
            email: "",
            phone: "",
            invalidPhone: false,
            avatar: "",
            previewAvatar: "",
            oldPassword: "",
            password: "",
            confirmPassword: "",
        },
        passwordShow: false,
        addDialogShow: false,
        infoUpdateLoading: false,
        addressSelectedForEdit: {}
    }),
    components: {
        VueTelInput,
        AddressDialog,
    },
    validations: {
        form: {
            name: { required },
            // email: {
            //     email,
            //     required
            // },
            // oldPassword: { required},
            password: { minLength: minLength(6) },
            confirmPassword: { sameAsPassword: sameAs('password') }
        }
    },
    computed: {
        ...mapGetters( "auth",[
            "currentUser"
        ]),
        ...mapGetters('app',[
            'availableCountries'
        ]),
        ...mapGetters("address",[
            "getAddresses",
            "addressesLoaded",
            "getDefaultShippingAddress",
            "getDefaultBillingAddress"
        ]),
        nameErrors() {
            const errors = [];
            if (!this.$v.form.name.$dirty) return errors;
            !this.$v.form.name.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required &&
                errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email &&
                errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
            return errors;
        },
        oldPasswordErrors() {
            // const errors = [];
            // if (!this.$v.form.oldPassword.$dirty) return errors;
            // !this.$v.form.oldPassword.required &&
            //     errors.push(this.$i18n.t("Este campo es requerido"))
            // return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.minLength &&
                errors.push(this.$i18n.t("password_must_be_minimum_6_characters"));
            return errors;
        },
        confirmPasswordErrors() {
            const errors = [];
            if (!this.$v.form.confirmPassword.$dirty) return errors;
            !this.$v.form.confirmPassword.sameAsPassword &&
                errors.push(this.$i18n.t("password_and_confirm_password_should_match"));
            return errors;
        }
    },
    created(){
        this.form.name = this.currentUser.name
        this.form.email = this.currentUser.email
        this.form.phone = this.currentUser.phone
        this.form.previewAvatar = this.currentUser.avatar

        this.fetchAddresses();
    },
    methods: {
        ...mapMutations("auth",[
            "setUser"
        ]),
        ...mapMutations("address",[
            "setAddresses"
        ]),
        ...mapActions("address",[
            "fetchAddresses",
        ]),
		previewThumbnail(event) {
            this.form.avatar = event.target.files[0]
			if (event.target.files && event.target.files[0]) {
				const reader = new FileReader();
				reader.onload = (e) => {
					this.form.previewAvatar = e.target.result;
				};
				reader.readAsDataURL(event.target.files[0]);
			}
		},
        phoneValidate(phone){
            this.form.invalidPhone = (phone.valid) ? false : true ;
        },
        async updateBasic() {

            // if(this.form.email == ""){
            //     this.snack({
            //         message: "Email is required.",
            //         color: "red"
            //     });
            //     return;
            // }
            // if(this.form.email != "" && !this.$v.form.email.email){
            //     this.snack({
            //         message: "Email must be valid.",
            //         color: "red"
            //     });
            //     return;
            // }
            // if(this.form.phone != ""  && this.form.invalidPhone){
            //     this.snack({
            //         message: "Phone number must be valid.",
            //         color: "red"
            //     });
            //     return;
            // }
    
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            
            // this.form.phone = this.form.phone.replace(/\s/g, '')
            this.infoUpdateLoading = true;

            
            let formData = new FormData();
            for ( var key in this.form ) {
                formData.append(key, this.form[key]);
            }

            const res = await this.call_api("post", "user/info/update", formData,true);
            if (res.data.success) {
                this.setUser(res.data.user)
                this.snack({ message: res.data.message, });
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }
            this.infoUpdateLoading = false;
        },
        async deleteAddress(id){
            const res = await this.call_api("get", `user/address/delete/${id}`);
            if(res.data.success){
                this.setAddresses(res.data.data)
                this.snack({ message: res.data.message });
            }
        },
        editAddress(address){
            this.addressSelectedForEdit = address
            this.addDialogShow = true
        },
        async markDefaultShipping(id){
            const res = await this.call_api("get", `user/address/default-shipping/${id}`);
            if(res.data.success){
                this.setAddresses(res.data.data)
                this.snack({ message: res.data.message });
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async markDefaultBilling(id){
            const res = await this.call_api("get", `user/address/default-billing/${id}`);
            if(res.data.success){
                this.setAddresses(res.data.data)
                this.snack({ message: res.data.message });
            }else{
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        addressDialogClosed(){
            this.addressSelectedForEdit = {}
            this.addDialogShow = false;
        },
	},
}
</script>