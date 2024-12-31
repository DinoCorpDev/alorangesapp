<template>
    <v-dialog content-class="modal-register" v-model="showRegister">
        <v-card class="modal-register-card">
            <v-card-title style="background-color: #f58634; color:white; position: relative; padding: 12px 0; min-height: 56px;" class="text-xs-center justify-center title white--text font-weight-bold">
                <span v-if="registerNotification == false">REGISTRO</span>
                <button @click="closeRegister" class="esc-button">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.15128 1.15128C1.61991 0.682647 2.3797 0.682647 2.84833 1.15128L7.9998 6.30275L13.1513 1.15128C13.6199 0.682647 14.3797 0.682647 14.8483 1.15128C15.317 1.61991 15.317 2.3797 14.8483 2.84833L9.69686 7.9998L14.8483 13.1513C15.317 13.6199 15.317 14.3797 14.8483 14.8483C14.3797 15.317 13.6199 15.317 13.1513 14.8483L7.9998 9.69686L2.84833 14.8483C2.3797 15.317 1.61991 15.317 1.15128 14.8483C0.682647 14.3797 0.682647 13.6199 1.15128 13.1513L6.30275 7.9998L1.15128 2.84833C0.682647 2.3797 0.682647 1.61991 1.15128 1.15128Z" fill="white"/>
                    </svg>
                </button>
            </v-card-title>
            <v-form
                class="modal-register-form"
                ref="loginForm"
                lazy-validation
                @submit.prevent="register()"
                enctype="multipart/form-data"
            >
                <v-stepper v-model="numberPag" v-if="registerNotification == false">
                    <v-stepper-header>
                        <v-stepper-step
                            :class="numberPag > 1 ? 'v-stepper__step--complete' : ''"
                            class="modal-register-step"
                            step="1"
                        />

                        <v-divider :class="numberPag > 1 ? 'step-active' : ''" />

                        <v-stepper-step
                            :class="numberPag > 2 ? 'v-stepper__step--complete' : ''"
                            class="modal-register-step"
                            step="2"
                        />

                        <v-divider :class="numberPag > 2 ? 'step-active' : ''" />

                        <v-stepper-step class="modal-register-step" step="3" />
                    </v-stepper-header>

                    <v-divider class="modal-register-subheader" />

                    <v-stepper-items>
                        <v-stepper-content step="1">
                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        {{ $t("CORREO ELECTRÓNICO O NÚMERO DE TELÉFONO") }}
                                    </span>

                                    <div class="input-group">
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar correo electrónico o teléfono"
                                            type="email"
                                            v-model="form.email"
                                            :error-messages="emailErrors"
                                            @blur="$v.form.email.$touch()"
                                            required
                                        />
                                    </div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        {{ $t("CONTRASEÑA") }}
                                    </span>

                                    <v-text-field
                                        v-model="form.password"
                                        placeholder="Ingresar contraseña"
                                        :error-messages="passwordErrors"
                                        @blur="$v.form.password.$touch()"
                                        :type="passwordShow ? 'text' : 'password'"
                                        :append-icon="passwordShow ? 'las la-eye' : 'las la-eye-slash'"
                                        class="input-group--focused place-holder"
                                        hide-details="auto"
                                        required
                                        dense
                                        outlined
                                        @click:append="passwordShow = !passwordShow"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        {{ $t("REPETIR CONTRASEÑA") }}
                                    </span>

                                    <v-text-field
                                        v-model="form.confirmPassword"
                                        placeholder="Ingresar contraseña"
                                        :error-messages="confirmPasswordErrors"
                                        @blur="$v.form.confirmPassword.$touch()"
                                        :type="passwordShowConfirm ? 'text' : 'password'"
                                        :append-icon="passwordShowConfirm ? 'las la-eye' : 'las la-eye-slash'"
                                        class="input-group--focused place-holder"
                                        hide-details="auto"
                                        required
                                        dense
                                        outlined
                                        @click:append="passwordShowConfirm = !passwordShowConfirm"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" sm="12">
                                    <v-row>
                                        <v-col cols="10">
                                            <CustomCheckbox
                                                inputValue="Natural"
                                                label="Registrar como persona natural"
                                                name="personType"
                                                type="radio"
                                                v-model="form.personType"
                                                style="width: 110%; display: inline-flex"
                                                bordered
                                            />
                                        </v-col>
                                        <v-col cols="2" class="icono-persona">
                                            <v-tooltip bottom color="black">
                                                <template v-slot:activator="{ on, attrs }">
                                                    <i
                                                        class="las la-exclamation-circle icon-tooltip-dos"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        style="font-size: 30px; transform: rotate(180deg)"
                                                    ></i>
                                                </template>
                                                <span>Se aplicaran los impuestos al</span>
                                                <br />
                                                <span>facturar como persona juridica</span>
                                            </v-tooltip>
                                        </v-col>
                                    </v-row>
                                </v-col>

                                <v-col cols="12" sm="12" style="margin-bottom: 50px">
                                    <v-row>
                                        <v-col cols="10">
                                            <CustomCheckbox
                                                inputValue="Juridical"
                                                label="Registrar como persona jurídica"
                                                name="personType"
                                                type="radio"
                                                v-model="form.personType"
                                                style="width: 110%; display: inline-flex"
                                                bordered
                                            />
                                        </v-col>
                                        <v-col cols="2" class="icono-persona">
                                            <v-tooltip bottom color="black">
                                                <template v-slot:activator="{ on, attrs }">
                                                    <i
                                                        class="las la-exclamation-circle icon-tooltip-dos"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        style="font-size: 30px; transform: rotate(180deg)"
                                                    ></i>
                                                </template>
                                                <span>Se aplicaran los impuestos al</span>
                                                <br />
                                                <span>facturar como persona juridica</span>
                                            </v-tooltip>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-stepper-content>

                        <v-stepper-content step="2">
                            <template v-if="form.personType == 'Juridical'"><label><b>DATOS PERSONA DE COMPRAS</b></label></template>
                            <v-row class="pt-3">
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase">Primer Nombre</span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar nombre"
                                        v-model="form.firstName"
                                        :error-messages="firstNameErrors"
                                        @blur="$v.form.firstName.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Segundo Nombre (Opcional) </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar segundo nombre"
                                        v-model="form.secondName"
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Primer Apellido </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar primer apellido"
                                        v-model="form.firstLastname"
                                        :error-messages="firstLastnameErrors"
                                        @blur="$v.form.firstLastname.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Segundo Apellido(Opcional) </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar segundo apellido"
                                        v-model="form.secondLastname"
                                    />
                                </v-col>
                            </v-row>
                            
                            <v-row v-if="form.personType == 'Juridical'">
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Celular </span>
                                    <CustomInput
                                        type="number"
                                        class="place-holder"
                                        placeholder="Ingresar celular"
                                        v-model="form.cellphonePurchasingPerson"
                                        :error-messages="cellphonePurchasingPersonErrors"
                                        @blur="$v.form.cellphonePurchasingPerson.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Telefono fijo </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar telefono fijo"
                                        v-model="form.phonePurchasingPerson"
                                        :error-messages="phonePurchasingPersonErrors"
                                        @blur="$v.form.phonePurchasingPerson.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="12">
                                    <span class="black--text body-2 text-uppercase"> Correo </span>
                                    <CustomInput
                                        class="place-holder"
                                        type="email"
                                        placeholder="Ingresar Correo"
                                        v-model="form.emailPurchasingPerson"
                                        :error-messages="emailPurchasingPersonErrors"
                                        @blur="$v.form.emailPurchasingPerson.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase"> Documento</span>
                                    <SelectCustom
                                        placeholder="Seleccionar tipo de documento"
                                        class="select-placeholder"
                                        :items="documentTypes"
                                        v-model="form.documentType"
                                        :error-messages="documentTypeErrors"
                                        @blur="$v.form.documentType.$touch()"
                                        required
                                    />
                                    
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">
                                        Numero de Documento
                                    </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar número de documento"
                                        v-model="form.documentNumber"
                                        :error-messages="documentNumberErrors"
                                        @blur="$v.form.documentNumber.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <template v-if="form.personType == 'Juridical'">
                                <div class="mb-3 mt-5">
                                    <label><b>DATOS DE LA EMPRESA</b></label>
                                </div>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">
                                            Razón Social de la Empresa
                                        </span>
                                        <CustomInput
                                            placeholder="Ingrese razón social de la empresa"
                                            class="place-holder"
                                            v-model="form.companyRazon"
                                            :error-messages="companyRazonErrors"
                                            @blur="$v.form.companyRazon.$touch()"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">Entidad Comercial</span>
                                        <SelectCustom
                                            placeholder="Seleccione entidad comercial"
                                            class="select-placeholder"
                                            :items="companyTypes"
                                            v-model="form.companyType"
                                            :error-messages="companyTypeErrors"
                                            @blur="$v.form.companyType.$touch()"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">
                                            Numero de NIT
                                        </span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Indicar número de NIT"
                                            v-model="form.companyDocumentNumber"
                                            :error-messages="companyDocumentNumberErrors"
                                            @blur="$v.form.companyDocumentNumber.$touch()"
                                            required
                                        />
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="12">
                                        <span class="black--text body-2 text-uppercase">
                                            {{ $t("CORREO ELECTRÓNICO (ÁREA CONTABLE)") }}
                                        </span>

                                        <div class="input-group">
                                            <CustomInput
                                                class="place-holder"
                                                placeholder="Ingresar correo electrónico)"
                                                type="email"
                                                v-model="form.companyEmail"
                                                :error-messages="companyEmailErrors"
                                                @blur="$v.form.companyEmail.$touch()"
                                                required
                                            />
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="12">
                                        <span class="black--text body-2 text-uppercase">
                                            Teléfono / Celular (Área Contable)
                                        </span>
                                        <v-row>
                                            <v-col cols="12">
                                                <vue-tel-input
                                                    placeholder="Ingresar teléfono / celular"
                                                    v-model="form.companyPhone"
                                                    v-bind="mobileInputProps"
                                                    :onlyCountries="availableCountries"
                                                    @blur="$v.form.companyPhone.$touch()"
                                                    :class="{
                                                        'error--text':
                                                            $v.form.companyPhone.$error || form.showInvalidPhone
                                                    }"
                                                    class="place-holder"
                                                >
                                                    <template slot="arrow-icon">
                                                        <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                    </template>
                                                </vue-tel-input>
                                                <div
                                                    class="v-text-field__details mt-2 pl-3"
                                                    v-if="$v.form.companyPhone.$error"
                                                >
                                                    <div class="v-messages theme--light error--text" role="alert">
                                                        <div class="v-messages__wrapper">
                                                            <div class="v-messages__message">
                                                                {{ $t("this_field_is_required") }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="v-text-field__details mt-2 pl-3"
                                                    v-if="!$v.form.companyPhone.$error && form.showInvalidPhone"
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
                                    <!-- <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">
                                            {{ $t("ACTIVIDAD ECONOMICA (CÓDIGO CIIU)") }}
                                        </span>

                                        <div class="input-group">
                                            <SelectCustom
                                                placeholder="Seleccione actividad economica"
                                                class="select-placeholder"
                                                :items="codigoCiiuTypes"
                                                v-model="form.companyActividad"
                                                :error-messages="actividadErrors"
                                                :multiple="true"
                                                @blur="$v.form.companyActividad.$touch()"
                                                required
                                            />
                                        </div>
                                    </v-col> -->

                                    <!-- <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">REGIMEN FISCAL</span>

                                        <div class="input-group">
                                            <SelectCustom
                                            placeholder="Seleccione regimen fiscal"
                                            class="select-placeholder"
                                            :items="regimenTypes"
                                            v-model="form.regimenFiscal"
                                            :multiple="true"
                                            :error-messages="regimenFiscalErrors"
                                            @blur="$v.form.regimenFiscal.$touch()"
                                            required
                                        />
                                        </div>
                                    </v-col> -->
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2 text-uppercase">RESPONSABILIDAD TRIBUTARIA </span>
                                        <SelectCustom
                                            placeholder="Seleccione responsabilidad tributaria"
                                            class="select-placeholder"
                                            :items="responsabilidadTypes"
                                            v-model="form.responsabilidadTribut"
                                            :multiple="false"
                                            :error-messages="responsabilidadTributErrors"
                                            @blur="$v.form.responsabilidadTribut.$touch()"
                                            required
                                        />
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <!-- <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2 text-uppercase"> DOCUMENTO (ARCHIVO) </span>
                                    </v-col> -->

                                    <!-- <v-col cols="11" class="area-upload">
                                        <v-file-input
                                            placeholder="SUBIR DOCUMENTO"
                                            class="upload"
                                            v-model="form.filedocumento"
                                            accept="application/pdf"
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <span>SUBIR DOCUMENTO</span>
                                            </template>
                                        </v-file-input>
                                    </v-col> -->

                                    <!-- <v-col cols="1" class="icono-documento">
                                        <v-tooltip bottom color="black">
                                            <template v-slot:activator="{ on, attrs }">
                                                <i
                                                    class="las la-exclamation-circle icon-tooltip-dos"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    style="font-size: 30px; transform: rotate(180deg)"
                                                ></i>
                                            </template>
                                            <span>&bullet; Documentos en formato PDF</span>
                                            <br />
                                            <span>&bullet; Ambas caras de ID ampliadas al 150</span>
                                            <br />
                                            <span>&bullet; Documento legible y en color</span>
                                        </v-tooltip>
                                    </v-col> -->
                                </v-row>

                                <v-row>
                                    <!-- <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2 text-uppercase">
                                            Numero de CAMARA DE COMERCIO (ARCHIVO)
                                        </span>
                                    </v-col> -->
                                    <!-- <v-col cols="11" class="area-upload">
                                        <v-file-input
                                            class="form-control upload"
                                            v-model="form.filecamara"
                                            accept="application/pdf"
                                            single-line
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <span>SUBIR DOCUMENTO</span>
                                            </template>
                                        </v-file-input>
                                    </v-col> -->
                                    <!-- <v-col cols="1" class="icono-documento"> -->
                                        <!--<i class="las la-exclamation-circle icon-tooltip-dos" style="font-size: 35px; transform: rotate(180deg);" data-title=" - Documentos en formato PDF.\n- RUT actualizado"></i>-->
                                        <!-- <v-tooltip bottom color="black">
                                            <template v-slot:activator="{ on, attrs }">
                                                <i
                                                    class="las la-exclamation-circle icon-tooltip-dos"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    style="font-size: 30px; transform: rotate(180deg)"
                                                ></i>
                                            </template>
                                            <span>&bullet; Documentos en formato PDF</span>
                                            <br />
                                            <span>&bullet; Cámara de comercio no mayor a 90 días</span>
                                        </v-tooltip>
                                    </v-col> -->
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2 text-uppercase"> RUT (ARCHIVO) </span>
                                    </v-col>
                                    <v-col cols="11" class="area-upload">
                                        <v-file-input
                                            placeholder="SUBIR DOCUMENTO"
                                            class="form-control upload"
                                            v-model="form.filerut"
                                            accept="application/pdf"
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <span>SUBIR DOCUMENTO</span>
                                            </template>
                                        </v-file-input>
                                    </v-col>
                                    <v-col cols="1" class="icono-documento">
                                        <v-tooltip bottom color="black">
                                            <template v-slot:activator="{ on, attrs }">
                                                <i
                                                    class="las la-exclamation-circle icon-tooltip-dos"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    style="font-size: 30px; transform: rotate(180deg)"
                                                ></i>
                                            </template>
                                            <span>&bullet; Documentos en formato PDF</span>
                                            <br />
                                            <span>&bullet; RUT actualizado</span>
                                        </v-tooltip>
                                    </v-col>
                                </v-row>
                            </template>
                        </v-stepper-content>

                        <v-stepper-content step="3">
                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">País</span>
                                    <SelectCustom
                                        placeholder="Seleccionar país"
                                        class="select-placeholder"
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
                            </v-row>

                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        Nombre De Dirección (Casa / Oficina)
                                    </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar nombre de dirección"
                                        v-model="mainAddress.name"
                                        :error-messages="addressNameErrors"
                                        @blur="$v.mainAddress.name.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase"> Dirección (Calle / Carrera) </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar calle / carrera"
                                        v-model="mainAddress.address"
                                        :error-messages="addressErrors"
                                        @blur="$v.mainAddress.address.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        Dirección Adicional (Piso / Apartamento / Oficina)
                                    </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar Piso / Apartamento / Oficina"
                                        v-model="mainAddress.details"
                                        :error-messages="addressDetailsErrors"
                                        @blur="$v.mainAddress.details.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Codigo Postal</span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Seleccione codigo postal"
                                        v-model="mainAddress.postal_code"
                                    />
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Departamento</span>
                                    <SelectCustom
                                        placeholder="Seleccionar departamento"
                                        class="select-placeholder"
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
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Municipio</span>
                                    <SelectCustom
                                        placeholder="Seleccionar municipio"
                                        class="select-placeholder"
                                        :error-messages="cityErrors"
                                        :items="filteredCities"
                                        @blur="$v.mainAddress.city.$touch()"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="mainAddress.city"
                                    />
                                </v-col>
                            </v-row>

                            <v-row>
                                <!-- <v-col cols="12" md="6">
                                    <span class="black--text body-2 text-uppercase">Localidad</span>
                                    <SelectCustom
                                        :items="filteredLocalidad"
                                        required
                                        v-model="mainAddress.localidad"
                                        @blur="$v.mainAddress.localidad.$touch()"
                                        :error-messages="localidadErrors"
                                    />
                                </v-col> -->

                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Barrio ( Opcional )</span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar barrio"
                                        v-model="mainAddress.neighborhood"
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Teléfono / Celular</span>
                                    <v-row>
                                        <v-col cols="12">
                                            <vue-tel-input
                                                placeholder="Ingresar teléfono / celular"
                                                v-model="form.phone"
                                                v-bind="mobileInputProps"
                                                :onlyCountries="availableCountries"
                                                @blur="$v.form.phone.$touch()"
                                                :class="{
                                                    'error--text': $v.form.phone.$error || form.showInvalidPhone
                                                }"
                                                class="place-holder"
                                            >
                                                <template slot="arrow-icon">
                                                    <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                </template>
                                            </vue-tel-input>
                                            <div class="v-text-field__details mt-2 pl-3" v-if="$v.form.phone.$error">
                                                <div class="v-messages theme--light error--text" role="alert">
                                                    <div class="v-messages__wrapper">
                                                        <div class="v-messages__message">
                                                            {{ $t("this_field_is_required") }}
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
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>

                <div
                    align="center"
                    style="height: 300px; margin-top: 50px; margin-right: 20px; margin-left: 20px"
                    v-if="registerNotification"
                >
                    <img src="/public/assets/img/checked.png" alt="Checkeado">
                    <!-- <i class="las la-check" style="color: green; font-size: 80px"></i> -->
                    <br /><br />

                    <h5 align="center"><b>¡Gracias por registrarse!</b></h5>
                    <br /><br />
                    <p class="black--text">
                        Hemos enviado una notificación de confirmación a su dirección de correo electrónico registrado.
                    </p>
                </div>
            </v-form>

            <v-card-actions class="pa-5" v-if="registerNotification == false">
                <CustomButtonR
                    v-if="numberPag > 1"
                    icon="la-angle-left"
                    text="Volver"
                    color="orange2"
                    type="button"
                    @click="before"
                    text-class="mr-6"
                />
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <CustomButtonR
                    v-if="numberPag == 3"
                    text="Omitir paso"
                    color="white2"
                    type="button"
                    @click="omitir"
                    class="boton-redondo"
                    :disabled="loadingregister"
                    :loadingregister="loadingregister"
                />
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <CustomButtonR
                    v-if="numberPag < 3"
                    icon="la-angle-right"
                    iconPosition="right"
                    text="Continuar"
                    color="orange"
                    type="button"
                    class="boton-continuar"
                    text-class="ml-6"
                    @click="after"
                />
                <CustomButtonR
                    v-if="numberPag == 3"
                    icon="la-angle-right"
                    iconPosition="right"
                    text="Registrame"
                    color="orange"
                    type="submit"
                    text-class="ml-6"
                    @click="register"
                    :disabled="loadingregister"
                    :loading="loadingregister"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { VueTelInput } from "vue-tel-input";
import { mapActions, mapGetters, mapMutations } from "vuex";
import { required, requiredIf, email, minLength, sameAs, helpers } from "vuelidate/lib/validators";

import CustomButtonR from "../../components/global/CustomButtonRegister.vue";
import CustomCheckbox from "../../components/global/CustomCheckbox.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import snackbar from "../../components/inc/SnackBar";
import ArrowUpload from "../../components/icons/ArrowUpload.vue";

// Custom validators
const isTrue = value => value === true;
const passwordStrong = helpers.regex('passwordStrong', /(?=(.*[0-9]))(?=.*[\!@#$%^&*()\\[\]{}\-_+=|:;"'<>,./?])(?=.*[a-z])(?=(.*[A-Z]))(?=(.*)).{8,}/);

export default {
    props: {
        value: Boolean
    },
    components: {
        ArrowUpload,
        CustomButtonR,
        CustomCheckbox,
        CustomInput,
        SelectCustom,
        VueTelInput,
        snackbar
    },
    data() {
        return {
            registerNotification: false,
            mobileInputProps: {
                inputOptions: {
                    type: "tel",
                    placeholder: "Ingresar teléfono / celular"
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
            // filteredLocalidad: [],
            documentTypes: [
                { text: "(C.C) Cedula de ciudadanía", value: "C.C" },
                { text: "(C.E) Cédula de Extranjería", value: "C.E" },
                { text: "(PAS) Pasaporte", value: "PAS" },
                { text: "(NIT) NIT", value: "NIT" },
            ],
            responsabilidadTypes:[
                { value: "01", text: "Responsable de IVA" },
                { value: "02", text: "No responsable de IVA" },
                { value: "03", text: "Impuesto nacional al consumo - INC" },
                { value: "04", text: "No responsable de INC" },
                { value: "05", text: "Responsable de IVA e INC" },
                { value: "06", text: "Regimen especial" }
            ],
            regimenTypes:[
                { value:"O-13", text: "O-13" },
                { value:"O-15", text: "O-15" },
                { value:"O-23", text: "O-23" },
                { value:"O-47", text: "O-47" },
                { value:"R-99", text: "R-99" },
            ],
            codigoCiiuTypes: [],
            codigoPostalTypes: [],
            companyTypes: [
                { text: "(S.A.S) Sociedad por acciones simplificadas", value: "S.A.S" },
                { text: "(S.A) Sociedad Anónima", value: "S.A" },
                { text: "LTDA Sociedad Limitada", value: "LTDA" },
                { text: "S.C Sociedad Colectiva", value: "S.C"},
                { text: "S.C.A Sociedad comanditaria & Cia", value: "S.C.A"}
            ],
            form: {
                email: "",
                password: "",
                confirmPassword: "",
                personType: "Natural",
                firstName: "",
                firstLastname: "",
                secondName:"",
                secondLastname: "",
                documentType: "",
                documentNumber: "",
                companyRazon: "",
                companyType: "",
                companyDocumentType: "",
                companyDocumentNumber: "",
                companyActividad: "",
                companyPhone: "",
                companyEmail: "",
                regimenFiscal: "",
                responsabilidadTribut: "",
                phone: "",
                policiesAndCookiesConsent: false,
                offersConsent: false,
                invalidPhone: true,
                showInvalidPhone: false,
                emailPurchasingPerson:"",
                cellphonePurchasingPerson:"",
                phonePurchasingPerson:"",
                // filedocumento: [],
                // filecamara: [],
                filerut: []
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
                // localidad: "",
                postal_code: "",
                phone: "",
                type: "shipping"
            },
            loadingregister: false,
            numberPag: 1,
            passwordShow: false,
            passwordShowConfirm: false
        };
    },
    validations: {
        form: {
            email: { required, email },
            password: { required, minLength: minLength(6), passwordStrong},
            confirmPassword: { required, sameAsPassword: sameAs("password") },
            personType: { required },
            firstName: { required },
            firstLastname: { required },
            documentType: { required },
            documentNumber: { required },
            companyRazon: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyType: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyDocumentNumber: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            //companyActividad: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyPhone: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            // regimenFiscal: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            responsabilidadTribut: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            companyEmail: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            emailPurchasingPerson: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            cellphonePurchasingPerson: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            phonePurchasingPerson: { requiredIf: requiredIf(item => item.personType === "Juridical") },
            phone: { required }
        },
        mainAddress: {
            address: { required },
            name: { required },
            details: { required },
            country: { required },
            state: { required },
            // localidad: { required },
            city: { required },
            // postal_code: { required }
        }
    },
    computed: {
        ...mapGetters("app", ["generalSettings", "availableCountries"]),
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("*Inserte un email válido"));
            return errors;
        },
        companyEmailErrors() {
            const errors = [];
            if (!this.$v.form.companyEmail.$dirty) return errors;
            !this.$v.form.companyEmail.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            !this.$v.form.password.minLength && errors.push(this.$i18n.t("*La contraseña debe tener mínimo 6 carácteres"));
            !this.$v.form.password.passwordStrong && errors.push("*La contraseña necesita al menos 1 caracter especial, 1 mayúscula y 1 minúscula");
            
            return errors;
        },
        confirmPasswordErrors() {
            const errors = [];
            if (!this.$v.form.confirmPassword.$dirty) return errors;
            !this.$v.form.confirmPassword.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            !this.$v.form.confirmPassword.sameAsPassword &&
                errors.push(this.$i18n.t("*Las contraseñas no coinciden"));
            return errors;
        },
        personTypeErrors() {
            const errors = [];
            if (!this.$v.form.personType.$dirty) return errors;
            !this.$v.form.personType.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        firstNameErrors() {
            const errors = [];
            if (!this.$v.form.firstName.$dirty) return errors;
            !this.$v.form.firstName.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        firstLastnameErrors() {
            const errors = [];
            if (!this.$v.form.firstLastname.$dirty) return errors;
            !this.$v.form.firstLastname.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        documentTypeErrors() {
            const errors = [];
            if (!this.$v.form.documentType.$dirty) return errors;
            !this.$v.form.documentType.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        documentNumberErrors() {
            const errors = [];
            if (!this.$v.form.documentNumber.$dirty) return errors;
            !this.$v.form.documentNumber.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },

        cellphonePurchasingPersonErrors() {
            const errors = [];
            if (!this.$v.form.cellphonePurchasingPerson.$dirty) return errors;
            !this.$v.form.cellphonePurchasingPerson.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },

        emailPurchasingPersonErrors() {
            const errors = [];
            if (!this.$v.form.emailPurchasingPerson.$dirty) return errors;
            !this.$v.form.emailPurchasingPerson.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },

        phonePurchasingPersonErrors() {
            const errors = [];
            if (!this.$v.form.phonePurchasingPerson.$dirty) return errors;
            !this.$v.form.phonePurchasingPerson.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },

        companyRazonErrors() {
            const errors = [];
            if (!this.$v.form.companyRazon.$dirty) return errors;
            !this.$v.form.companyRazon.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyTypeErrors() {
            const errors = [];
            if (!this.$v.form.companyType.$dirty) return errors;
            !this.$v.form.companyType.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        // regimenFiscalErrors() {
        //     const errors = [];
        //     if (!this.$v.form.regimenFiscal.$dirty) return errors;
        //     !this.$v.form.regimenFiscal.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        responsabilidadTributErrors() {
            const errors = [];
            if (!this.$v.form.responsabilidadTribut.$dirty) return errors;
            !this.$v.form.responsabilidadTribut.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.form.companyDocumentType.$dirty) return errors;
            !this.$v.form.companyDocumentType.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.form.companyDocumentNumber.$dirty) return errors;
            !this.$v.form.companyDocumentNumber.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        // actividadErrors() {
        //     const errors = [];
        //     if (!this.$v.form.companyActividad.$dirty) return errors;
        //     !this.$v.form.companyActividad.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        addressErrors() {
            const errors = [];
            if (!this.$v.mainAddress.address.$dirty) return errors;
            !this.$v.mainAddress.address.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        addressNameErrors() {
            const errors = [];
            if (!this.$v.mainAddress.name.$dirty) return errors;
            !this.$v.mainAddress.name.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        addressDetailsErrors() {
            const errors = [];
            if (!this.$v.mainAddress.details.$dirty) return errors;
            !this.$v.mainAddress.details.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.mainAddress.country.$dirty) return errors;
            !this.$v.mainAddress.country.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.mainAddress.state.$dirty) return errors;
            !this.$v.mainAddress.state.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.mainAddress.city.$dirty) return errors;
            !this.$v.mainAddress.city.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        // localidadErrors() {
        //     const errors = [];
        //     if (!this.$v.mainAddress.localidad.$dirty) return errors;
        //     !this.$v.mainAddress.localidad.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        // postalCodeErros() {
        //     const errors = [];
        //     if (!this.$v.mainAddress.postal_code.$dirty) return errors;
        //     !this.$v.mainAddress.postal_code.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        // fileDocumentoErrors() {
        //     const errors = [];
        //     if (!this.$v.form.filedocumento.$dirty) return errors;
        //     !this.$v.form.filedocumento.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        // fileCamaraErrors() {
        //     const errors = [];
        //     if (!this.$v.form.filecamara.$dirty) return errors;
        //     !this.$v.form.filecamara.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        fileRutErrors() {
            const errors = [];
            if (!this.$v.form.filerut.$dirty) return errors;
            !this.$v.form.filerut.requiredIf && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        showRegister: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit("input", value);
            }
        }
    },
    created() {
        this.resetData();
        this.registerNotification = false;
        this.numberPag = 1;
        this.fetchCountries();
        this.fetchCodigoCiiu();
        this.fetchCodigoPostal();
    },
    methods: {
        ...mapActions("auth", ["login"]),
        ...mapMutations("auth", ["updateChatWindow", "showLoginDialog"]),
        closeRegister(){
            this.showRegister = false;
        },
        phoneValidate(phone) {
            this.form.invalidPhone = phone.valid ? false : true;
            if (phone.valid) this.form.showInvalidPhone = false;
        },
        // onFileChange(e) {
        //     this.form.filedocumento = e.target.files[0];
        // },
        // onFileChangeCamara(e) {
        //     this.form.filecamara = e.target.files[0];
        // },
        onFileChangeRut(e) {
            this.form.filerut = e.target.files[0];
        },
        async omitir() {
            if (
                this.form.firstName == "" ||
                this.form.firstLastname == "" ||
                this.form.documentType == "" ||
                this.form.documentNumber == ""
            ) {
                this.$v.form.$touch();
                return;
            }

            if (this.form.personType == "Juridical") {
                if (
                    this.form.companyRazon == "" ||
                    this.form.companyType == "" ||
                    this.form.companyDocumentNumber == "" ||
                    this.form.companyActividad == "" ||
                    this.form.companyPhone == "" ||
                    this.form.companyEmail == "" || 
                    this.form.regimenFiscal == "" ||
                    this.form.responsabilidadTribut == "" 
                ) {
                    this.$v.form.$touch();
                    return;
                }

                if (
                    // this.form.filecamara.length == 0 ||
                    // this.form.filedocumento.length == 0 ||
                    this.form.filerut.length == 0
                ) {
                    this.snack({
                        message: "Por favor cargue los archivos!",
                        color: "red"
                    });
                    return;
                }
            }

            this.form.phone = this.form.phone.replace(/\s/g, "");
            this.mainAddress.phone = this.form.phone;

            this.loadingregister = true;
            let formData = new FormData();

            // formData.append("filecamara", this.form.filecamara);
            // formData.append("filedocumento", this.form.filedocumento);
            formData.append("filerut", this.form.filerut);
            formData.append("form", JSON.stringify(this.form));

            const res = await this.call_api("post", "auth/signup", formData, true);

            if (res.data.success) {
                this.mainAddress.customer_id = res.data.user.id;

                this.snack({
                    message: res.data.message,
                    color: "green"
                });

                // await this.saveAddress().then(() => {
                //     this.showLoginDialog(false);
                //     this.updateChatWindow(false);
                // });

                this.resetData();
                this.registerNotification = true;
                //this.showRegister = false;
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }

            this.loadingregister = false;
        },
        async register() {
            this.$v.form.$touch();
            this.$v.mainAddress.$touch();

            /*if (this.form.invalidPhone) {
                this.form.showInvalidPhone = true;
                return;
            }*/

            if (this.$v.form.$anyError || this.$v.mainAddress.$anyError) {
                console.log(this.$v.form);
                console.log(this.$v.mainAddress.$anyError);
                console.log('pasa por aqui');
                return;
            }

            if (this.form.personType == "Juridical") {
                if (
                    // this.form.filecamara.length == 0 ||
                    // this.form.filedocumento.length == 0 ||
                    this.form.filerut.length == 0
                ) {
                    this.snack({
                        message: "Por favor cargue los archivos!",
                        color: "red"
                    });
                    return;
                }
            }

            this.form.phone = this.form.phone.replace(/\s/g, "");
            this.mainAddress.phone = this.form.phone;

            this.loadingregister = true;
            let formData = new FormData();

            //formData.append("filecamara", this.form.filecamara);
            //formData.append("filedocumento", this.form.filedocumento);
            formData.append("filerut", this.form.filerut);
            formData.append("form", JSON.stringify(this.form));

            const res = await this.call_api("post", "auth/signup", formData, true);

            if (res.data.success) {
                this.mainAddress.customer_id = res.data.user.id;

                this.snack({
                    message: res.data.message,
                    color: "green"
                });

                await this.saveAddress().then(() => {
                     this.showLoginDialog(false);
                     this.updateChatWindow(false);
                });

                this.resetData();
                this.registerNotification = true;
                //this.showRegister = false;
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
            }

            this.loadingregister = false;
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

            // const res2 = await this.call_api("get", `localidades/${stateid}`);

            if (res.data.success) {
                this.filteredCities = res.data.data;
                this.form.city = "";

                // if (res2.data.success) {
                //     this.filteredLocalidad = res2.data.data;
                //     this.form.localidad = "";
                // }

            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async fetchCodigoCiiu() {
            const res = await this.call_api("get", "all-codigo-ciiu");

            if (res.data.success) {   
                this.codigoCiiuTypes = res.data.data;
            }
        },
        async fetchCodigoPostal() {
            const res = await this.call_api("get", "all-codigo-postal");
            if (res.data.success) {
                this.codigoPostalTypes = res.data.data;
            }
        },
        async saveAddress() {
            this.mainAddress.phone = this.form.phone;

            try {
                const res = await this.call_api("post", "user/address/createRegister", this.mainAddress);

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
        },
        async after() {
            if (this.numberPag == 1) {
                if (
                    this.form.email == "" ||
                    this.form.password == "" ||
                    this.form.confirmPassword == "" ||
                    this.form.password != this.form.confirmPassword||
                    this.$v.form.password.$invalid
                ) {
                    this.$v.form.$touch();
                    return;
                }
            }

            if (this.numberPag == 2) {
                if (
                    this.form.firstName == "" ||
                    this.form.firstLastname == "" ||
                    this.form.documentType == "" ||
                    this.form.documentNumber == ""
                ) {
                    this.$v.form.$touch();
                    return;
                }

                if (
                    this.form.personType == "Juridical" &&
                    (this.form.companyRazon == "" ||
                        this.form.companyType == "" ||
                        this.form.companyDocumentNumber == "" ||
                        this.form.companyEmail == "" ||
                        // this.form.companyActividad == "" ||
                        this.form.companyPhone == "" ||
                        // this.form.regimenFiscal == "" ||
                        this.form.responsabilidadTribut == "")
                ) {
                    this.$v.form.$touch();
                    return;
                }

                if (
                    this.form.personType == "Juridical" &&
                    (
                        // this.form.filecamara.length == 0 ||
                        // this.form.filedocumento.length == 0 ||
                        this.form.filerut.length == 0)
                ) {
                    this.snack({
                        message: "Por favor cargue los archivos!",
                        color: "red"
                    });
                    return;
                }
            }

            return (this.numberPag += 1);
        },
        async before() {
            return (this.numberPag -= 1);
        },
        resetData() {
            this.form.email = "";
            this.form.password = "";
            this.form.confirmPassword = "";
            this.form.personType = "Natural";
            this.form.firstName = "";
            this.form.firstLastname = "";
            this.form.documentType = "";
            this.form.regimenFiscal = "";
            this.form.responsabilidadTribut = "";
            this.form.documentNumber = "";
            this.form.companyRazon = "";
            this.form.companyType = "";
            this.form.companyDocumentType = "";
            this.form.companyDocumentNumber = "";
            this.form.companyActividad = "";
            this.form.companyPhone = "";
            this.form.companyEmail = "";
            this.form.emailPurchasingPerson = "";
            this.form.cellphonePurchasingPerson = "";
            this.form.phonePurchasingPerson = "";
            // this.form.filecamara = "";
            // this.form.filedocumento = "";
            this.form.filerut = "";
            this.form.phone = "";
            this.form.policiesAndCookiesConsent = false;
            this.form.offersConsent = false;
            this.form.invalidPhone = true;
            this.form.showInvalidPhone = false;

            this.mainAddress.customer_id = null;
            this.mainAddress.address = "";
            this.mainAddress.name = "";
            this.mainAddress.details = "";
            this.mainAddress.country = "";
            this.mainAddress.state = "";
            this.mainAddress.city = "";
            this.mainAddress.neighborhood = "";
            this.mainAddress.postal_code = "";
            this.mainAddress.phone = "";
            this.mainAddress.type = "shipping";

            this.numberPag = 1;

            this.$v.form.$reset();
            this.$v.mainAddress.$reset();
        }
    }
};
</script>

<style lang="scss">

.modal-register {
    max-width: 550px;

    /* width */
    &::-webkit-scrollbar {
        width: 3px;
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #b5b5b5;
        border-radius: 1px;
    }

    /* Handle */
    &::-webkit-scrollbar-thumb {
        background: rgba(#5a5a5a, 0.8);
        border-radius: 1px;
    }

    /* Handle on hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #5a5a5a;
    }
}
.custom-checkbox {
    &-checkmark {
        margin-left: 5px;
    }
}

.icon-tooltip-uno[data-title]:hover::after {
    content: attr(data-title);
    position: absolute;
    /*top: 70%;*/
    bottom: 80%;
    left: 0;
    z-index: 999;
    color: white;
    background-color: grey;
    font-size: 15px;
    padding: 20px;
    width: 300px;
    border-radius: 10px;
    font-style: normal;
    transform: rotate(180deg);
}

.icon-tooltip-dos[data-title]:hover::after {
    content: attr(data-title);
    position: absolute;
    /*top: 70%;*/
    bottom: 80%;
    left: 0;
    z-index: 500;
    color: white;
    background-color: grey;
    font-size: 15px;
    padding: 20px;
    width: 300px;
    border-radius: 10px;
    font-style: normal;
    transform: rotate(180deg);
    opacity: 1;
}
</style>

<style lang="scss" scoped>
::v-deep .v-stepper__step__step.primary{
    background-color: #f58634 !important;
    
}
.modal-register {
    &-card {
        border-radius: 10px;
    }

    .v-stepper__header {
        box-shadow: none;

        .v-divider {
            border-color: rgba(#707070, 0.5) !important;
            border-width: 2px 0 0;

            &.step-active {
                border-color: #707070 !important;
            }
        }
    }

    &-subheader {
        width: 92%;
        margin: auto;
        border-width: 2px 0 0;
    }

    .v-stepper {
        box-shadow: none;

        &.theme--light {
            &::v-deep {
                .v-stepper__step {
                    &__step.primary{
                        background-color: #f58634 !important;
                    }
                    &:not(.v-stepper__step--active) {
                        .v-stepper__step__step {
                            background: rgba(#000000, 0.25);
                        }
                    }

                    &.v-stepper__step--complete {
                        .v-stepper__step__step {
                            background-color: #f58634 !important;
                        }
                    }
                }
            }
        }
    }

    &-step {
        &::v-deep {
            .v-stepper__step__step {
                font-size: 15px;
                line-height: 1;
                width: 27px;
                height: 27px;
                padding-top: 5px;
                margin-right: 0;
            }
        }
    }
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

.place-holder {
    font-family: "Roboto", sans-serif;
    font-size: 15px;
    letter-spacing: 0.5px;
    border-radius: 10px;

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

.select-placeholder{

    &::v-deep{

        ::placeholder{

            font-style: italic;
        }
    }
}

.v-btn {
    &::v-deep {
        .v-btn__content {
            margin-top: 2px;
        }
    }
}

.boton-redondo {
    height: 100px;
    width: 150px;
    border: 1px solid #e2e2e2;
    border-radius: 100px;
}

.boton-redondo:hover {
    background: #dfdfdf;
    border: 1px solid #e2e2e2;
}

.upload {
    border-radius: 100px;
    width: 100%;

     &::v-deep {
        .v-label {
            top: 24% !important;
            left: 6.5% !important;
            right: 0% !important;
            text-align: center;
        }

        .v-input__slot {
            cursor: pointer;
        }

        .v-text-field__slot {
            .v-label--active {
                display: none;
            }
        }
     }
}

.theme--light {
    .v-text-field:not(.v-input--has-state) {
        &::v-deep {
            &:hover {
                .v-input__control {
                    .v-input__slot {
                        &::before {
                            border-color: white;
                        }
                    }
                }
            }
        }
    }

    .v-text-field {
        &::v-deep {
            .v-input__control {
                .v-input__slot {
                    &::before {
                        border-color: white;
                    }
                }
            }
        }
    }
}

.icono-documento {
    display: flex;
    align-items: center;
    margin-bottom: 7.5px;
    justify-content: right;
}

.texto-upload {
    padding-top: 0px;
    padding-bottom: 0px;
}

.area-upload {
    padding-top: 0px;
    padding-bottom: 0px;
}

.upload {
    &::v-deep {
        .v-input__prepend-outer {
            display: none !important;
        }
    }
}

// ACTUALIZACIÓN 04/10/2023 J.M

.icono-persona {
    display: flex;
    justify-content: right;
    align-items: center;
    text-align: right;
}
.custom-checkbox {
    border-color: #cfcfcf;
    color: #cfcfcf;
}
.custom-checkbox:hover {
    border-color: grey;
    color: grey;
}

.custom-checkbox-checkmark:active {
    border-color: black;
    color: black;
}

.border-checked {
    .custom-checkbox {
        border-color: black;
        color: black;
    }
}
.esc-button{
    position: absolute;
    top: 25%;
    right: 5%;
    padding: 0;
    margin: 0;
    &:hover{
        path{
            fill: #fcd6ba;
        }
    }
}
</style>
