<template>
    <div class="px-5 py-3">
        <v-row align="center">
            <v-col cols="6" class="text-start">
                <div class="avatar-upload">
                    <custom-button color="grey" text="Cambiar Foto" @click="changeAvatar" />
                    <input
                        hidden
                        type="file"
                        ref="avatar-input"
                        id="avatar-input"
                        accept="image/png, image/jpg, image/jpeg"
                        @change="previewThumbnail"
                    />
                </div>
            </v-col>
            <v-col cols="6" class="text-end"> Se unio en {{ formatDate(currentUser.registerSince) }} </v-col>
        </v-row>
        <v-divider class="my-6" />
        <address-dialog
            :typeAddress="typeAddress"
            :show="addDialogShow"
            @close="addressDialogClosed"
            :old-address="addressSelectedForEdit"
        />
        <profile-dialog :show="profileDialogShow" @close="profileDialogClosed" :old-profile="profileSelectedForEdit" />
        <password-dialog
            :show="passwordDialogShow"
            @close="passwordDialogClosed"
            :old-password="passwordSelectedForEdit"
        />
        <factory-dialog :show="factoryDialogShow" @close="factoryDialogClosed" :old-factory="factorySelectedForEdit" />
        <v-row>
            <v-col cols="12" md="6">
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="bold">Usuario principal</h5>
                    <v-divider class="my-4" />
                    <v-form
                        class="form-profile"
                        lazy-validation
                        v-on:submit.prevent="updateBasic()"
                        enctype="multipart/form-data"
                    >
                        <input type="email" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                        <input type="password" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">{{ $t("email_address") }}</label>
                            </v-col>
                            <v-col class="text-end">{{ currentUser.email || "--" }} </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Tipo de Persona</label>
                            </v-col>
                            <v-col class="text-end"> {{ currentUser.personType || "--" }} </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">{{ $t("name") }}</label>
                            </v-col>
                            <v-col class="text-end">{{ currentUser.name || "--" }}</v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Documento</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ currentUser.documentType || "--" }} {{ currentUser.documentNumber || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <custom-button color="grey" text="Editar" @click="editProfile()" />
                            </v-col>
                        </v-row>

                        <v-divider class="my-4" />

                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Nombre de Dirección</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.name || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Dirección</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.address }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Descripción de Dirección</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.details || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Código Postal</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.postal_code || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Departamento</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.state || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Municipio</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.city || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Barrio</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.neighborhood || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Nombre de Quién Va a Recibir</label>
                            </v-col>
                            <v-col class="text-end">
                                {{ currentUser.name }}
                            </v-col>
                        </v-row>
                        <v-row class="mb-3">
                            <v-col class="text-start">
                                <label class="profile-label">Teléfono / Mobil </label>
                            </v-col>
                            <v-col class="text-end">
                                {{ defaultAddress.phone || "--" }}
                            </v-col>
                        </v-row>
                        <custom-button color="grey" text="Editar" @click="editAddress(defaultAddress, 'shipping')" />
                    </v-form>
                </v-card>
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="bold">Otras direcciones</h5>
                    <v-divider class="my-4" />

                    <div class="form" v-for="(otherAdd, i) in otherAdress" :key="i">
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                            <span class="body1 text-right">{{ otherAdd?.address }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Descripción de Dirección</span>
                            <span class="body1 text-right">{{ otherAdd?.address }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                            <span class="body1">{{ otherAdd?.postal_code }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                            <span class="body1">{{ otherAdd?.state }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                            <span class="body1">{{ otherAdd?.city }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                            <span class="body1"> -- </span>
                        </div>
                        <div class="d-flex justify-space-between mb-2">
                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                            <span class="body1">{{ otherAdd?.phone }}</span>
                        </div>
                        <custom-button
                            class="mr-3"
                            color="grey"
                            text="Editar"
                            @click="editAddress(otherAdd, 'billing')"
                        />

                        <custom-button class="mr-3" color="red" text="Eliminar" @click="deleteAddress(otherAdd?.id)" />

                        <v-divider class="my-4" />
                    </div>

                    <div>
                        <custom-button class="my-4" block color="grey" @click="openAdress('billing')"
                            >AÑADIR DIRECCION</custom-button
                        >
                        <div class="cards">
                            Puedes registrar otras direcciones para los envios de facturacion, entregas de productos y
                            solicitudes de servicio.
                        </div>
                    </div>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="bold">Empresa</h5>
                    <v-divider class="my-4" />
                    <div>
                        <custom-button class="my-4" block color="grey" @click="editFactory()">
                            AÑADIR MI EMPRESA
                        </custom-button>
                        <div class="cards">
                            Realiza las compras como persona juridica agregando los datos de facturación como empresa.
                        </div>
                    </div>
                </v-card>
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="bold">Contraseña</h5>
                    <v-divider class="my-4" />
                    <div>
                        <custom-button class="my-4" block color="grey" @click="editPassword()">
                            CAMBIAR CONTRASEÑA
                        </custom-button>
                    </div>
                </v-card>
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="bold">Terminos y condiciones</h5>
                    <v-divider class="my-4" />
                    <div class="tyc">
                        <label class="label">
                            <input type="checkbox" />
                            <span class="body-1 black--text text cards">
                                Quiero recibir comunicaciones comerciales personalizadas de idovela a través de email.
                                <b>Conocer newsletter.</b>
                            </span>
                            <span class="checkmark"></span>
                        </label>
                        <label class="label">
                            <input type="checkbox" />
                            <span class="body-1 black--text text cards">
                                He podido leer y entiendo la <b>Política de privacidad y cookies.</b>
                            </span>
                            <span class="checkmark"></span>
                        </label>
                        <label class="label">
                            <input type="checkbox" />
                            <span class="body-1 black--text text cards">
                                Acepto los <b>Terminos y condiciones para la compra con idovela.</b>
                            </span>
                            <span class="checkmark"></span>
                        </label>
                        <label class="label">
                            <input type="checkbox" />
                            <span class="body-1 black--text text cards"> Acepto las <b>Polizas de garantia.</b> </span>
                            <span class="checkmark"></span>
                        </label>
                        <label class="label">
                            <input type="checkbox" />
                            <span class="body-1 black--text text cards">
                                Acepto los terminos de <b>La logistica de envió.</b>
                            </span>
                            <span class="checkmark"></span>
                        </label>
                        <custom-button color="grey" text="SABER MÁS" />
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from "vuex";
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import CustomButton from "../../components/global/CustomButton.vue";
import { VueTelInput } from "vue-tel-input";
import AddressDialog from "../../components/address/AddressDialog.vue";
import ProfileDialog from "../../components/user/ProfileDialog.vue";
import PasswordDialog from "../../components/user/PasswordDialog.vue";
import FactoryDialog from "../../components/user/FactoryDialog.vue";

import CustomInput from "../../components/global/CustomInput.vue";

export default {
    data: () => ({
        mobileInputProps: {
            inputOptions: {
                type: "tel",
                placeholder: "phone number"
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
        form: {
            name: "",
            email: "",
            phone: "",
            invalidPhone: false,
            avatar: "",
            previewAvatar: "",
            oldPassword: "",
            password: "",
            confirmPassword: ""
        },
        passwordShow: false,
        addDialogShow: false,
        profileDialogShow: false,
        passwordDialogShow: false,
        factoryDialogShow: false,
        infoUpdateLoading: false,
        addressSelectedForEdit: {},
        profileSelectedForEdit: {},
        passwordSelectedForEdit: {},
        factorySelectedForEdit: {},
        defaultAddress: {},
        otherAdress: [],
        typeAddress: "shipping"
    }),
    components: {
        VueTelInput,
        AddressDialog,
        ProfileDialog,
        PasswordDialog,
        FactoryDialog,
        CustomButton,
        CustomInput
    },
    validations: {
        form: {
            name: { required },
            oldPassword: { required },
            password: { minLength: minLength(6) },
            confirmPassword: { sameAsPassword: sameAs("password") }
        }
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapGetters("app", ["availableCountries"]),
        ...mapGetters("address", [
            "getAddresses",
            "addressesLoaded",
            "getDefaultShippingAddress",
            "getDefaultBillingAddress"
        ]),
        nameErrors() {
            const errors = [];
            if (!this.$v.form.name.$dirty) return errors;
            !this.$v.form.name.required && errors.push(this.$i18n.t("this_field_is_required"));
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("this_field_is_required"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("this_field_is_required_a_valid_email"));
            return errors;
        },
        oldPasswordErrors() {
            const errors = [];
            if (!this.$v.form.oldPassword.$dirty) return errors;
            !this.$v.form.oldPassword.required && errors.push(this.$i18n.t("this_field_is_required"));
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.minLength && errors.push(this.$i18n.t("password_must_be_minimum_6_characters"));
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
    created() {
        this.getUser();
        this.getAddressUser();
    },
    methods: {
        ...mapActions("auth", ["getUser"]),
        ...mapMutations("app", ["setPreviewAvatar"]),
        ...mapMutations("auth", ["setUser"]),
        ...mapMutations("address", ["setAddresses"]),
        changeAvatar() {
            this.$refs["avatar-input"].click();
        },
        previewThumbnail(event) {
            this.form.avatar = event.target.files[0];
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    this.form.previewAvatar = e.target.result;
                    this.setPreviewAvatar(e.target.result);
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        },
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
        },
        async getAddressUser() {
            const res = await this.call_api("get", `user/addresses`);
            if (res.data.success) {
                this.defaultAddress = res.data?.data?.find(address => address.default_shipping == 1);
                this.otherAdress = res.data?.data?.filter(address => address.default_shipping == 0);
            }
        },
        async updateBasic() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }

            this.infoUpdateLoading = true;

            let formData = new FormData();
            for (var key in this.form) {
                formData.append(key, this.form[key]);
            }

            const res = await this.call_api("post", "user/info/update", formData, true);
            if (res.data.success) {
                this.setUser(res.data.user);
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }
            this.infoUpdateLoading = false;
        },
        async deleteAddress(id) {
            const res = await this.call_api("get", `user/address/delete/${id}`);
            if (res.data.success) {
                this.getAddressUser();
                this.snack({ message: res.data.message });
            }
        },
        async markDefaultShipping(id) {
            const res = await this.call_api("get", `user/address/default-shipping/${id}`);
            if (res.data.success) {
                this.setAddresses(res.data.data);
                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async markDefaultBilling(id) {
            const res = await this.call_api("get", `user/address/default-billing/${id}`);
            if (res.data.success) {
                this.setAddresses(res.data.data);
                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        editAddress(address, type) {
            this.typeAddress = type;
            this.addressSelectedForEdit = address;
            this.addDialogShow = true;
        },
        editProfile() {
            this.profileSelectedForEdit = this.currentUser;
            this.profileDialogShow = true;
        },
        editPassword() {
            this.passwordSelectedForEdit = this.currentUser;
            this.passwordDialogShow = true;
        },
        editFactory() {
            this.factorySelectedForEdit = this.currentUser;
            this.factoryDialogShow = true;
        },
        addressDialogClosed() {
            this.addressSelectedForEdit = {};
            this.addDialogShow = false;
            this.getAddressUser();
        },
        profileDialogClosed() {
            this.profileSelectedForEdit = {};
            this.profileDialogShow = false;
            this.getUser();
        },
        passwordDialogClosed() {
            this.passwordSelectedForEdit = {};
            this.passwordDialogShow = false;
            this.getUser();
        },
        factoryDialogClosed() {
            this.factorySelectedForEdit = {};
            this.factoryDialogShow = false;
            this.getUser();
        },
        openAdress(type) {
            this.typeAddress = type;
            this.addDialogShow = true;
        },
        formatDate(date) {
            const d = new Date(date);
            const month = d.toLocaleString("default", { month: "long" }).toLocaleLowerCase();

            return `${this.$i18n.t(month)} de ${d.getFullYear()}`;
        }
    }
};
</script>

<style scoped lang="scss">
.form-profile {
    line-height: 20px;
}

@media (max-width: 600px) {
    .form-profile {
        line-height: 14px;
        font-size: 12px;
    }
    .tyc {
        line-height: 20px;
    }
    .date {
        font-size: 12px;
    }
    .cards {
        font-size: 12px !important;
    }
}

.form-border {
    border: 1px solid #e4e4e4;
}

.profile {
    &-label {
        text-transform: uppercase;
        font-size: 15px;
        font-weight: 700;
        line-height: 25.2px;
    }
}

//Estilos de input checkbox

.label {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 22px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.label input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
}

.label:hover input ~ .checkmark {
    background-color: #f5f5f5;
}

.label input:checked ~ .checkmark {
    background-color: #000000;
    border-radius: 5px;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.label input:checked ~ .checkmark:after {
    display: block;
}

.label .checkmark:after {
    left: 9px;
    top: 5px;
    width: 7px;
    height: 12px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
</style>
