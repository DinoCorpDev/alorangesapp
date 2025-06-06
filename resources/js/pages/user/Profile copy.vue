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
                <h5 class="fw-600">Información</h5>
                <v-divider class="my-4" />
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5" v-if="!editarUser">
                    <h5 class="fw-600" style="display: inline;">Usuario principal</h5>
                    <i class="las la-star"></i>
                    <i class="las la-eye-slash" v-if="mostrarDatos" @click="ocultarDatosUsuario"></i>
                    <i class="las la-eye" @click="mostrarDatosUsuario" v-else></i>
                    <v-divider class="my-4" />

                        <div v-if="mostrarDatos">
                            <input type="email" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                            <input type="password" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">CORREO ELECTRONICO</label>
                                </v-col>
                                <v-col class="text-end">{{ currentUser.email || "--" }} </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">TIPO DE PERSONA</label>
                                </v-col>
                                <v-col class="text-end"> {{ currentUser.personType || "--" }} </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">NOMBRE COMPLETO</label>
                                </v-col>
                                <v-col class="text-end">{{ currentUser.name || "--" }}</v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">TIPO DE DOCUMENTO</label>
                                </v-col>
                                <v-col class="text-end">
                                    {{ currentUser.documentType || "--" }}
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">DOCUMENTO</label>
                                </v-col>
                                <v-col class="text-end">
                                    {{ currentUser.documentNumber || "--" }}
                                </v-col>
                            </v-row>
                        </div>
                        <div v-else>
                            <input type="email" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                            <input type="password" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">CORREO ELECTRONICO</label>
                                </v-col>
                                <v-col class="text-end">******@******.*** </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">TIPO DE PERSONA</label>
                                </v-col>
                                <v-col class="text-end"> {{ currentUser.personType || "--" }} </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">NOMBRE COMPLETO</label>
                                </v-col>
                                <v-col class="text-end">******* ******** *********</v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">TIPO DE DOCUMENTO</label>
                                </v-col>
                                <v-col class="text-end">
                                    {{ currentUser.documentType || "--" }}
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-start">
                                    <label class="profile-label">DOCUMENTO</label>
                                </v-col>
                                <v-col class="text-end">
                                    *****************
                                </v-col>
                            </v-row>
                        </div>
                        <v-row>
                            <v-col>
                                <custom-button color="grey" text="Editar" @click="editProfile()" />
                            </v-col>
                        </v-row>

                        <v-divider class="my-4" />
                </v-card>

                 <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5" v-else>
                    <h5 class="fw-600">Editar usuario principal</h5>
                    
                    <v-form class="inputs" ref="loginForm" lazy-validation>
                        <v-row>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">Primer Nombre</span>
                                <custom-input
                                    v-model="formUser.firstName"
                                    :error-messages="firstNameErrors"
                                    @blur="$v.formUser.firstName.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase"> Segundo Nombre (Opcional) </span>
                                <custom-input v-model="formUser.secondName" />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">Primer Apellido</span>
                                <custom-input
                                    v-model="formUser.firstLastname"
                                    :error-messages="firstLastnameErrors"
                                    @blur="$v.formUser.firstLastname.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">Segundo Apellido</span>
                                <custom-input
                                    v-model="formUser.secondLastname"
                                    :error-messages="secondLastnameErrors"
                                    @blur="$v.formUser.secondLastname.$touch()"
                                    required
                                />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">Documento (Representante)</span>
                                <select-custom
                                    :items="documentTypes"
                                    v-model="formUser.documentType"
                                    :error-messages="documentTypeErrors"
                                    @blur="$v.formUser.documentType.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">Numero de Documento</span>
                                <custom-input
                                    v-model="formUser.documentNumber"
                                    :error-messages="documentNumberErrors"
                                    @blur="$v.formUser.documentNumber.$touch()"
                                    required
                                />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2 text-uppercase">
                                    {{ $t("password") }}
                                </span>
                                <custom-input
                                    v-model="formUser.oldPassword"
                                    :error-messages="oldPasswordErrors"
                                    @blur="$v.formUser.oldPassword.$touch()"
                                    type="password"
                                    required
                                />
                                <custom-button
                                    block
                                    class="mt-5"
                                    text="Guardar"
                                    type="submit"
                                    color="black"
                                    @click="updateInfoUser"
                                    :disabled="infoUpdateLoading"
                                    :loading="infoUpdateLoading"
                                />
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>

                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="fw-600">Direcciones</h5>
                    <v-divider class="my-4" />

                    <div class="form" v-for="(otherAdd, i) in otherAdress" :key="i">
                        <div v-if="otherAdd?.editar == false">
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Dirección</span>
                                <span class="body1 text-right">{{ otherAdd?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Descripción de Dirección</span>
                                <span class="body1 text-right">{{ otherAdd?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Codigo Postal</span>
                                <span class="body1">{{ otherAdd?.postal_code }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Departamento</span>
                                <span class="body1">{{ otherAdd?.state }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Municipio</span>
                                <span class="body1">{{ otherAdd?.city }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Barrio</span>
                                <span class="body1"> {{ (otherAdd?.neighborhood) ? otherAdd?.neighborhood : '--' }} </span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase fw-600">Telefono / Movil</span>
                                <span class="body1">{{ otherAdd?.phone }}</span>
                            </div>
                            <custom-button
                                class="mr-3"
                                color="grey"
                                text="Editar"
                                @click="editDirection(otherAdd)"
                            />

                            <custom-button class="mr-3" color="red" text="Eliminar" @click="deleteAddress(otherAdd?.id)" />
                        </div>

                        <div v-if="otherAdd?.editar == true">
                            <h5 class="fw-600">Editar Dirección</h5>
                            <v-form :validator="$v.otherAdd" autocomplete="chrome-off">
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Dirección (Calle / Carrera)</div>
                                    <CustomInput
                                        v-model="otherAdd.name"
                                        required
                                    />
                                </div>
                                
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">{{ $t("address") }}</div>
                                <CustomInput
                                        v-model="otherAdd.address"
                                        required
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">{{ $t("postal_code") }}</div>
                                    <v-text-field
                                        :placeholder="$t('postal_code')"
                                        type="text"
                                        v-model="otherAdd.postal_code"
                                        hide-details="auto"
                                        required
                                        outlined
                                    ></v-text-field>
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">{{ $t("country") }}</div>
                                    <SelectCustom
                                        :items="countries"    
                                        @input="countryChanged"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.country"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">{{ $t("state") }}</div>
                                    <SelectCustom
                                        :items="filteredStates"
                                        @input="stateChanged"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.state"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">City</div>
                                    <SelectCustom
                                        :items="filteredCities"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.city"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Barrio ( Opcional )</div>
                                    <CustomInput v-model="otherAdd.neighborhood" />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">{{ $t("phone_number") }}</div>
                                    <v-row>
                                        <v-col cols="12">
                                            <vue-tel-input
                                                v-model="otherAdd.phone"
                                                v-bind="mobileInputProps"
                                                :onlyCountries="availableCountries"
                                                @validate="phoneValidate"
                                                @blur="$v.formDirection.phone.$touch()"
                                                :class="{
                                                    'error--text': $v.formDirection.phone.$error || formDirection.showInvalidPhone
                                                }"
                                            >
                                                <template slot="arrow-icon">
                                                    <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                </template>
                                            </vue-tel-input>
                                            <div
                                                class="v-text-field__details mt-2 pl-3"
                                                v-if="$v.formDirection.phone.$error"
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
                                                v-if="!$v.formDirection.phone.$error && formDirection.showInvalidPhone"
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

                                    <v-row>
                                        <v-col cols="4" md="4">
                                            <custom-button
                                                block
                                                class="mt-4"
                                                text="< Cancelar"
                                                type="button"
                                                color="black"
                                                @click="cancelEditAddress(otherAdd)"
                                            />
                                        </v-col>
                                        <v-col  cols="4" md="4" style="margin-left: 33%">
                                            <custom-button
                                                block
                                                class="mt-4"
                                                text="Guardar >"
                                                type="submit"
                                                color="black"
                                                @click="saveEditAddress(otherAdd)"
                                                :disabled="infoUpdateLoading"
                                                :loading="infoUpdateLoading"
                                            />
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-form>
                        </div>

                        <v-divider class="my-4" />
                    </div>


                    <div v-if="addDirection">
                        <h5 class="fw-600">Añadir Dirección</h5>
                        <v-form :validator="$v.formDirection" autocomplete="chrome-off">
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">Dirección (Calle / Carrera)</div>
                                <CustomInput
                                    v-model="formDirection.name"
                                    :error-messages="addressNameErrors"
                                    @blur="$v.formDirection.name.$touch()"
                                    required
                                />
                            </div>
                            
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">{{ $t("address") }}</div>
                            <CustomInput
                                    v-model="formDirection.address"
                                    :error-messages="addressErrors"
                                    @blur="$v.formDirection.address.$touch()"
                                    required
                                />
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">{{ $t("postal_code") }}</div>
                                <v-text-field
                                    :placeholder="$t('postal_code')"
                                    type="text"
                                    v-model="formDirection.postal_code"
                                    :error-messages="postalCodeErrors"
                                    hide-details="auto"
                                    required
                                    outlined
                                ></v-text-field>
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">{{ $t("country") }}</div>
                                <SelectCustom
                                    :error-messages="countryErrors"
                                    :items="countries"
                                    @blur="$v.formDirection.country.$touch()"
                                    @input="countryChanged"
                                    item-text="name"
                                    item-value="id"
                                    required
                                    v-model="formDirection.country"
                                />
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">{{ $t("state") }}</div>
                                <SelectCustom
                                    :error-messages="stateErrors"
                                    :items="filteredStates"
                                    @blur="$v.formDirection.state.$touch()"
                                    @input="stateChanged"
                                    item-text="name"
                                    item-value="id"
                                    required
                                    v-model="formDirection.state"
                                />
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">City</div>
                                <SelectCustom
                                    :error-messages="cityErrors"
                                    :items="filteredCities"
                                    @blur="$v.formDirection.city.$touch()"
                                    item-text="name"
                                    item-value="id"
                                    required
                                    v-model="formDirection.city"
                                />
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">Barrio ( Opcional )</div>
                                <CustomInput v-model="formDirection.neighborhood" />
                            </div>
                            <div class="mb-3">
                                <div class="mb-1 fs-13 fw-500">{{ $t("phone_number") }}</div>
                                <v-row>
                                    <v-col cols="12">
                                        <vue-tel-input
                                            v-model="formDirection.phone"
                                            v-bind="mobileInputProps"
                                            :onlyCountries="availableCountries"
                                            @validate="phoneValidate"
                                            @blur="$v.formDirection.phone.$touch()"
                                            :class="{
                                                'error--text': $v.formDirection.phone.$error || formDirection.showInvalidPhone
                                            }"
                                        >
                                            <template slot="arrow-icon">
                                                <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                            </template>
                                        </vue-tel-input>
                                        <div
                                            class="v-text-field__details mt-2 pl-3"
                                            v-if="$v.formDirection.phone.$error"
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
                                            v-if="!$v.formDirection.phone.$error && formDirection.showInvalidPhone"
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

                                <v-row>
                                    <v-col  cols="4" md="4">
                                        <custom-button
                                            block
                                            class="mt-5"
                                            text="Cancelar"
                                            type="button"
                                            color="black"
                                            @click="cancelAddAddress"
                                        />
                                    </v-col>

                                    <v-col  cols="4" md="4" style="margin-left: 33%">
                                        <custom-button
                                            block
                                            class="mt-5"
                                            text="Guardar"
                                            type="submit"
                                            color="black"
                                            @click="saveAddress"
                                            :disabled="infoUpdateLoading"
                                            :loading="infoUpdateLoading"
                                        />
                                    </v-col>
                                </v-row>
                            </div>
                        </v-form>
                    </div>

                    <CustomButton
                        class="mb-4"
                        block
                        color="grey"
                        text="Añadir Dirección"
                        @click="addAddress"
                    />
                    <p class="mb-0">
                        Puedes registrar otras direcciones para los envios de facturacion, entregas de productos y
                        solicitudes de servicio.
                    </p>
                </v-card>

                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="fw-600">Contraseña</h5>
                    <v-divider class="my-4" />
                    <CustomButton block color="grey" text="Cambiar Contraseña" @click="editPassword()" />
                </v-card>
            </v-col>        
            <v-col cols="12" md="6">
                <h5 class="fw-600">Empresas</h5>
                <v-divider class="my-4" />
                <div  v-for="(emp, i) in empresas" :key="i">
                    
                    <div v-if="emp.editar == false">
                        <div v-if="emp.mostrarDatos">
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600" style="display: inline;">{{ emp.company_razon }}</h5>
                                <i class="las la-star"></i>
                                <i class="las la-eye-slash" v-if="emp.mostrarDatos" @click="ocultarDatosEmpresa(emp)"></i>
                                <i class="las la-eye" @click="mostrarDatosEmpresa(emp)" v-else></i>
                                <v-divider class="my-4" />
                                
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">CORREO ELECTRONICO</label>
                                    </v-col>
                                    <v-col class="text-end">{{ emp.company_razon || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TIPO DE PERSONA</label>
                                    </v-col>
                                    <v-col class="text-end"> {{ emp.person_type || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NOMBRE REPRESENTANTE</label>
                                    </v-col>
                                    <v-col class="text-end">{{ emp.first_name + " " + emp.first_lastname  || "--" }}</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TIPO DE DOCUMENTO</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.document_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NÚMERO DE DOCUMENTO</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.document_number || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">RAZÓN SOCIAL</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.company_razon || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">ENTIDAD COMERCIAL</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.company_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NÚMERO DE NIT</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.document_number || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">CORREO ELECTRÓNICO (FACTURACIÓN)</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.company_email || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TELÉFONO / CELULAR ( ÁREA CONTABLE )</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.company_phone || "--" }}
                                    </v-col>
                                </v-row>
                                <custom-button
                                    class="mr-3"
                                    color="grey"
                                    text="Editar"
                                    @click="editEmpresa(emp)"
                                />

                                <custom-button class="mr-3" color="red" text="Eliminar" @click="deleteEmpresa(emp?.id)" />
                            </v-card>
                        </div>

                        <div v-else>
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600" style="display: inline;">{{ emp.company_razon }}</h5>
                                <i class="las la-star"></i>
                                <i class="las la-eye-slash" v-if="emp.mostrarDatos" @click="ocultarDatosEmpresa(emp)"></i>
                                <i class="las la-eye" @click="mostrarDatosEmpresa(emp)" v-else></i>
                                <v-divider class="my-4" />
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">CORREO ELECTRONICO</label>
                                    </v-col>
                                    <v-col class="text-end">******@*******.*****</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TIPO DE PERSONA</label>
                                    </v-col>
                                    <v-col class="text-end"> {{ emp.person_type || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NOMBRE REPRESENTANTE</label>
                                    </v-col>
                                    <v-col class="text-end">********* **********</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TIPO DE DOCUMENTO</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.document_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NÚMERO DE DOCUMENTO</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        ************
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">RAZÓN SOCIAL</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        **************
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">ENTIDAD COMERCIAL</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        {{ emp.company_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">NÚMERO DE NIT</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        *************
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">CORREO ELECTRÓNICO (FACTURACIÓN)</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        *******@*****.****
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start">
                                        <label class="profile-label">TELÉFONO / CELULAR ( ÁREA CONTABLE )</label>
                                    </v-col>
                                    <v-col class="text-end">
                                        **********
                                    </v-col>
                                </v-row>

                                <custom-button
                                    class="mr-3"
                                    color="grey"
                                    text="Editar"
                                    @click="editEmpresa(emp)"
                                />

                                <custom-button class="mr-3" color="red" text="Eliminar" @click="deleteEmpresa(emp?.id)" />
                            </v-card>
                        </div>
                    </div>

                    
                </div>

                <div v-if="addEmpresa">
                    <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                        <h5 class="fw-600">Añadir Empresa</h5>
                        <v-form :validator="$v.formEmpresa" autocomplete="chrome-off">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase">Primer Nombre</span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar nombre"
                                        v-model="formEmpresa.firstName"
                                        :error-messages="companyFirstNameErrors"
                                        @blur="$v.formEmpresa.firstName.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Segundo Nombre (Opcional) </span>
                                    <CustomInput 
                                    class="place-holder"
                                    v-model="formEmpresa.secondName"
                                    placeholder="Ingresar segundo nombre"
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Primer Apellido </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar primer apellido"
                                        v-model="formEmpresa.firstLastname"
                                        :error-messages="companyFirstLastnameErrors"
                                        @blur="$v.formEmpresa.firstLastname.$touch()"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2 text-uppercase"> Segundo Apellido </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar segundo apellido"
                                        v-model="formEmpresa.secondLastname"
                                        :error-messages="companySecondLastnameErrors"
                                        @blur="$v.formEmpresa.secondLastname.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase"> Documento (Representante) </span>
                                    <SelectCustom
                                        placeholder="Seleccionar tipo de documento"
                                        :items="documentTypes"
                                        v-model="formEmpresa.documentType"
                                        :error-messages="companyPersonDocumentTypeErrors"
                                        @blur="$v.formEmpresa.documentType.$touch()"
                                        required
                                    />
                                </v-col>


                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">Numero de Documento</span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Ingresar número de documento"
                                        v-model="formEmpresa.documentNumber"
                                        :error-messages="companyPersonDocumentNumberErrors"
                                        @blur="$v.formEmpresa.documentNumber.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">Razón Social de la Empresa</span>
                                    <CustomInput
                                        placeholder="Ingrese razón social de la empresa"
                                        class="place-holder"
                                        v-model="formEmpresa.companyRazon"
                                        :error-messages="companyRazonErrors"
                                        @blur="$v.formEmpresa.companyRazon.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">Entidad Comercial</span>
                                    <SelectCustom
                                        placeholder="Seleccione entidad comercial"
                                        :items="companyTypes"
                                        v-model="formEmpresa.companyType"
                                        :error-messages="companyTypeErrors"
                                        @blur="$v.formEmpresa.companyType.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">Documento</span>
                                    <SelectCustom
                                        placeholder="Seleccione tipo de documento"
                                        :items="documentTypes"
                                        v-model="formEmpresa.companyDocumentType"
                                        :error-messages="companyDocumentTypeErrors"
                                        @blur="$v.formEmpresa.companyDocumentType.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2 text-uppercase">
                                        Numero de
                                        {{ formEmpresa.companyDocumentType ? formEmpresa.companyDocumentType : "Documento" }}
                                    </span>
                                    <CustomInput
                                        class="place-holder"
                                        placeholder="Indicar número de documento"
                                        v-model="formEmpresa.companyDocumentNumber"
                                        :error-messages="companyDocumentNumberErrors"
                                        @blur="$v.formEmpresa.companyDocumentNumber.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>

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
                                            v-model="formEmpresa.companyEmail"
                                            :error-messages="companyEmailErrors"
                                            @blur="$v.formEmpresa.companyEmail.$touch()"
                                            required 
                                        />
                                    </div>

                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">Teléfono / Celular (Área Contable)</span>
                                    <v-row>
                                        <v-col cols="12">
                                            <vue-tel-input
                                                placeholder="Ingresar teléfono / celular"
                                                v-model="formEmpresa.companyPhone"
                                                v-bind="mobileInputProps"
                                                :onlyCountries="availableCountries"
                                                @blur="$v.formEmpresa.companyPhone.$touch()"
                                                :class="{
                                                    'error--text': $v.formEmpresa.companyPhone.$error || formEmpresa.showInvalidPhone
                                                }"
                                                class="place-holder"
                                                >
                                                <template slot="arrow-icon">
                                                    <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                </template>
                                            </vue-tel-input>
                                            <div class="v-text-field__details mt-2 pl-3" v-if="$v.formEmpresa.companyPhone.$error">
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
                                                v-if="!$v.formEmpresa.companyPhone.$error && formEmpresa.showInvalidPhone"
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
                                <v-col cols="12" md="12">
                                    <span class="black--text body-2 text-uppercase">
                                        {{ $t("ACTIVIDAD ECONOMICA (CÓDIGO CIIU)") }}
                                    </span>

                                    <div class="input-group">
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar actividad economica"
                                            type="text"
                                            v-model="formEmpresa.companyActividad"
                                            :error-messages="companyActividadErrors"
                                            @blur="$v.formEmpresa.companyActividad.$touch()"
                                            required 
                                        />
                                    </div>

                                </v-col>
                            </v-row>


                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2 text-uppercase"> DOCUMENTO (ARCHIVO) </span>
                                </v-col>
                                <v-col cols="11" class="area-upload">
                                    <v-file-input
                                        placeholder="SUBIR DOCUMENTO"
                                        class="form-control upload"
                                        v-model="formEmpresa.filedocumento"
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
                                        <template v-slot:activator="{ on, attrs }" >
                                            
                                            <i class="las la-exclamation-circle icon-tooltip-dos" v-bind="attrs" v-on="on" style="font-size: 30px; transform: rotate(180deg);"></i> 
                                        </template>
                                        <span>&bullet; Documentos en formato PDF</span>
                                        <br>
                                        <span>&bullet; Ambas caras de ID ampliadas al 150</span>
                                        <br>
                                        <span>&bullet; Documento legible y en color</span>
                                    </v-tooltip>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2 text-uppercase">
                                        Numero de CAMARA DE COMERCIO (ARCHIVO)
                                    </span>
                                </v-col>
                                <v-col cols="11" class="area-upload">

                                    <!-- REVISAR--------------------------------- -->
                                    <v-file-input
                                        
                                        class="form-control upload"
                                        v-model="formEmpresa.filecamara"
                                        accept="application/pdf"
                                        single-line
                                    >
                                    
                                        <template v-slot:label>
                                            <ArrowUpload />
                                            <span>SUBIR DOCUMENTO</span>
                                        </template>

                                    </v-file-input>

                                    <!-- REVISAR--------------------------------- -->

                                </v-col>
                                <v-col cols="1" class="icono-documento">
                                    <!--<i class="las la-exclamation-circle icon-tooltip-dos" style="font-size: 35px; transform: rotate(180deg);" data-title=" - Documentos en formato PDF.\n- RUT actualizado"></i>-->
                                    <v-tooltip bottom color="black">
                                        <template v-slot:activator="{ on, attrs }" >
                                            
                                            <i class="las la-exclamation-circle icon-tooltip-dos" v-bind="attrs" v-on="on" style="font-size: 30px; transform: rotate(180deg);"></i> 
                                        </template>
                                        <span>&bullet; Documentos en formato PDF</span>
                                        <br>
                                        <span>&bullet; Cámara de comercio no mayor a 90 días</span>
                                    </v-tooltip>

                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2 text-uppercase">
                                        RUT (ARCHIVO)
                                    </span>
                                </v-col>
                                <v-col cols="11" class="area-upload">
                                    <v-file-input
                                        placeholder="SUBIR DOCUMENTO"
                                        class="form-control upload"
                                        v-model="formEmpresa.filerut"
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
                                        <template v-slot:activator="{ on, attrs }" >
                                            
                                            <i class="las la-exclamation-circle icon-tooltip-dos" v-bind="attrs" v-on="on" style="font-size: 30px; transform: rotate(180deg);"></i> 
                                        </template>
                                        <span>&bullet; Documentos en formato PDF</span>
                                        <br>
                                        <span>&bullet; RUT actualizado</span>
                                    </v-tooltip>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="4" md="4">
                                    <custom-button
                                        block
                                        class="mt-4"
                                        text="< Cancelar"
                                        type="button"
                                        color="black"
                                        @click="cancelAddEmpresa()"
                                    />
                                </v-col>
                                <v-col  cols="4" md="4" style="margin-left: 33%">
                                    <custom-button
                                        block
                                        class="mt-4"
                                        text="Guardar >"
                                        type="submit"
                                        color="black"
                                        @click="saveAddEmpresa()"
                                        :disabled="infoUpdateLoading"
                                        :loading="infoUpdateLoading"
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card>
                </div>
                
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5" v-if="addEmpresa == false">
                    <p class="mb-0">
                        Realiza las compras como persona juridica agregando los datos de facturación como empresa.
                    </p>
                    <CustomButton class="mb-4" block color="grey" @click="addEmpresa = true" text="Añadir mi Empresa"/>
                </v-card>

                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <h5 class="fw-600">Terminos y condiciones</h5>
                    <v-divider class="my-4" />
                    <div class="tyc mb-3">
                        <CustomCheckbox v-model="form.offersConsent">
                            Quiero recibir comunicaciones comerciales personalizadas de Idovela a través de email.
                            <a href="#">Conocer newsletter.</a>
                        </CustomCheckbox>
                        <CustomCheckbox v-model="form.policiesAndCookiesConsent">
                            He podido leer y entiendo la <a href="#">Política de privacidad y cookies.</a>
                        </CustomCheckbox>
                        <CustomCheckbox>
                            Acepto los <a href="#">Términos y condiciones para la compra con Idovela.</a>
                        </CustomCheckbox>
                        <CustomCheckbox> Acepto las <a href="#">Pólizas de garantía.</a> </CustomCheckbox>
                        <CustomCheckbox> Acepto los términos de <a href="#">La logística de envió.</a> </CustomCheckbox>
                    </div>
                    <CustomButton color="grey" text="Saber Más" />
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { required, requiredIf, email, minLength, sameAs } from "vuelidate/lib/validators";
import { mapActions, mapGetters, mapMutations } from "vuex";
import CustomButton from "../../components/global/CustomButton.vue";
import { VueTelInput } from "vue-tel-input";
import AddressDialog from "../../components/address/AddressDialog.vue";
import ProfileDialog from "../../components/user/ProfileDialog.vue";
import PasswordDialog from "../../components/user/PasswordDialog.vue";
import FactoryDialog from "../../components/user/FactoryDialog.vue";

import CustomInput from "../../components/global/CustomInput.vue";
import CustomCheckbox from "../../components/global/CustomCheckbox.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import ArrowUpload from "../../components/icons/ArrowUpload.vue";

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
        countriesLoaded: false,
        countries: [],
        filteredStates: [],
        filteredCities: [],
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
            policiesAndCookiesConsent: false,
            offersConsent: false
        },
        documentTypes: [
            { text: "(C.C) Cedula de ciudadanía", value: "C.C" },
            { text: "(N.I.T) Numero de identificación tributario", value: "N.I.T" }
        ],
        companyTypes: [{ text: "(S.A.S) Sociedad por acciones simplificadas", value: "S.A.S" }],
        formUser: {
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
            oldPassword: "",
            invalidPhone: true,
            showInvalidPhone: false
        },
        formDirection: {
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
        formEmpresa: {
            personType: "Juridical",
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
            companyActividad: "",
            companyPhone: "",
            companyEmail: "",
            filedocumento: [],
            filecamara: [],
            filerut: []
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
        empresas: [],
        typeAddress: "shipping",
        mostrarDatos: false,
        editarUser: false,
        addDirection: false,
        addEmpresa: false
    }),
    components: {
        VueTelInput,
        AddressDialog,
        ProfileDialog,
        PasswordDialog,
        FactoryDialog,
        CustomButton,
        CustomInput,
        CustomCheckbox,
        SelectCustom,
        ArrowUpload
    },
    validations: {
        form: {
            name: { required },
            oldPassword: { required },
            password: { minLength: minLength(6) },
            confirmPassword: { sameAsPassword: sameAs("password") }
        },
        formUser: {
            firstName: { required },
            firstLastname: { required },
            secondLastname: { required },
            documentType: { required },
            documentNumber: { required },
            oldPassword: { required }
        },
        formDirection: {
            address: { required },
            name: { required },
            postal_code: { required },
            country: { required },
            state: { required },
            city: { required },
            phone: { required },
        },
        formEmpresa: {
            firstName: { required },
            firstLastname: { required },
            secondLastname: { required },
            documentType: { required },
            documentNumber: { required },
            companyRazon: { required },
            companyType: { required },
            companyDocumentType: { required },
            companyDocumentNumber: { required },
            companyActividad: { required },
            companyPhone: { required },
            companyEmail: { required },
        },
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
            !this.$v.form.name.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("Este campo es requerido"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("Este campo es requerido_a_valid_email"));
            return errors;
        },
        oldPasswordErrors() {
            const errors = [];
            if (!this.$v.form.oldPassword.$dirty) return errors;
            !this.$v.form.oldPassword.required && errors.push(this.$i18n.t("Este campo es requerido"));
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
        },
        firstNameErrors() {
            const errors = [];
            if (!this.$v.formUser.firstName.$dirty) return errors;
            !this.$v.formUser.firstName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        secondNameErrors() {
            const errors = [];
            if (!this.$v.formUser.secondName.$dirty) return errors;
            !this.$v.formUser.secondName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        firstLastnameErrors() {
            const errors = [];
            if (!this.$v.formUser.firstLastname.$dirty) return errors;
            !this.$v.formUser.firstLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        secondLastnameErrors() {
            const errors = [];
            if (!this.$v.formUser.secondLastname.$dirty) return errors;
            !this.$v.formUser.secondLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        documentTypeErrors() {
            const errors = [];
            if (!this.$v.formUser.documentType.$dirty) return errors;
            !this.$v.formUser.documentType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        documentNumberErrors() {
            const errors = [];
            if (!this.$v.formUser.documentNumber.$dirty) return errors;
            !this.$v.formUser.documentNumber.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressErrors() {
            const errors = [];
            if (!this.$v.formDirection.address.$dirty){ return errors; }
            !this.$v.formDirection.address.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressNameErrors() {
            const errors = [];
            if (!this.$v.formDirection.name.$dirty){ return errors; }
            !this.$v.formDirection.name.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        addressDetailsErrors() {
            const errors = [];
            if (!this.$v.formDirection.details.$dirty){ return errors; }
            !this.$v.formDirection.details.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        postalCodeErrors() {
            const errors = [];
            if (!this.$v.formDirection.postal_code.$dirty){ return errors; }
            !this.$v.formDirection.postal_code.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.formDirection.country.$dirty){ return errors; }
            !this.$v.formDirection.country.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.formDirection.state.$dirty){ return errors; }
            !this.$v.formDirection.state.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.formDirection.city.$dirty){ return errors; }
            !this.$v.formDirection.city.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        phoneErrors() {
            const errors = [];
            if (!this.$v.formDirection.phone.$dirty){ return errors; }
            !this.$v.formDirection.phone.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyFirstNameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.firstName.$dirty) return errors;
            !this.$v.formEmpresa.firstName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companySecondNameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.secondName.$dirty) return errors;
            !this.$v.formEmpresa.secondName.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyFirstLastnameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.firstLastname.$dirty) return errors;
            !this.$v.formEmpresa.firstLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companySecondLastnameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.secondLastname.$dirty) return errors;
            !this.$v.formEmpresa.secondLastname.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyPersonDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.documentType.$dirty) return errors;
            !this.$v.formEmpresa.documentType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyPersonDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.documentNumber.$dirty) return errors;
            !this.$v.formEmpresa.documentNumber.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyRazonErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyRazon.$dirty) return errors;
            !this.$v.formEmpresa.companyRazon.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyType.$dirty) return errors;
            !this.$v.formEmpresa.companyType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyDocumentType.$dirty) return errors;
            !this.$v.formEmpresa.companyDocumentType.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyDocumentNumber.$dirty) return errors;
            !this.$v.formEmpresa.companyDocumentNumber.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyActividadErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyActividad.$dirty) return errors;
            !this.$v.formEmpresa.companyActividad.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
        companyEmailErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyEmail.$dirty) return errors;
            !this.$v.formEmpresa.companyEmail.required && errors.push(this.$i18n.t("Este campo es requerido"));
            return errors;
        },
    },
    async created() {
        this.fetchCountries();
        await this.getUser();
        this.getAddressUser();
        this.getEmpresasUser();

        this.form.policiesAndCookiesConsent = Boolean(this.currentUser.policiesAndCookiesConsent);
        this.form.offersConsent = Boolean(this.currentUser.offersConsent);
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
        async getEmpresasUser(){
            const res = await this.call_api("get", `user/companies`);
            if (res.data.success) {
                this.empresas = res.data.data;
            }
        },
        mostrarDatosUsuario(){
            this.mostrarDatos = true;
        },
        ocultarDatosUsuario(){
            this.mostrarDatos = false;
        },
        async updateInfoUser() {
            this.$v.formUser.$touch();

            if (this.$v.formUser.$anyError) {
                return;
            }

            // this.formUser.phone = this.formUser.phone.replace(/\s/g, "");

            this.infoUpdateLoading = true;

            const res = await this.call_api("post", "user/info/update", this.formUser);

            if (res.data.success) {
                this.setUser(res.data.user);
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: res.data.message, color: "red" });
            }

            this.editarUser = false;

            this.infoUpdateLoading = false;
        },
        async saveAddress() {
            this.$v.formDirection.$touch();
        
            if (this.$v.formDirection.$anyError) {
                return;
            }

            this.formDirection.phone = this.formDirection.phone.replace(/\s/g, "");

            this.adding = true;
            let data = {
                type: 'billing',
                ...this.formDirection
            };
            const res = await this.call_api("post", "user/address/create", data);
            if (res.data.success) {
                this.snack({ message: res.data.message });
                this.resetDataDirection();
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.getAddressUser();
            this.addDirection = false;
            this.adding = false;
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
            this.editarUser = true;
            this.formUser.firstName = this.currentUser.firstName;
            this.formUser.firstLastname = this.currentUser.firstLastname;
            this.formUser.secondLastname = this.currentUser.secondLastname;
            this.formUser.documentType = this.currentUser.documentType;
            this.formUser.documentNumber = this.currentUser.documentNumber;                
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
        addAddress(){
            this.addDirection = true;
        },
        cancelAddAddress(){
            this.addDirection = false;
        },
        async editDirection(direction){
            direction.country = direction.country_id;
            let country = await this.countryChanged(direction.country_id);
            direction.state = direction.state_id;

            let state = await this.stateChanged(direction.state_id);
            direction.city = direction.city_id;

            direction.editar = true;
        },
        cancelEditAddress(direction){
            direction.editar = false;
        },
        async saveEditAddress(direction){
            this.adding = true;

            if(direction.name == '' || direction.address == '' || direction.postal_code == '' || direction.country == '' || direction.state == '' || direction.city == '' || direction.phone == ''){
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
                this.adding = false;

                return;
            }
            const res = await this.call_api("post", `user/address/update`, direction);

            if (res.data.success) {
                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.adding = false;
            this.getAddressUser();
            direction.editar = false;
        },
        formatDate(date) {
            const d = new Date(date);
            const month = d.toLocaleString("default", { month: "long" }).toLocaleLowerCase();

            return `${this.$i18n.t(month)} de ${d.getFullYear()}`;
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
        resetDataDirection() {
            this.formDirection.id = null;
            this.formDirection.address = "";
            this.formDirection.name = "";
            this.formDirection.neighborhood = "";
            this.formDirection.postal_code = "";
            this.formDirection.country = "";
            this.formDirection.state = "";
            this.formDirection.city = "";
            this.formDirection.phone = "";

            this.$v.formDirection.$reset();
        },
        mostrarDatosEmpresa(emp){
            emp.mostrarDatos = true;
        },
        ocultarDatosEmpresa(emp){
            emp.mostrarDatos = false;
        },
        
        async saveAddEmpresa(){
            this.$v.formEmpresa.$touch();
        
            if (this.$v.formEmpresa.$anyError) {
                return;
            } 

            this.infoUpdateLoading = true;

            const res = await this.call_api("post", "user/companies/create", this.formEmpresa);
            if (res.data.success) {
                this.snack({ message: res.data.message });
                this.resetDataDirection();
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.getEmpresasUser();
            this.addEmpresa = false;
            this.infoUpdateLoading = false;

            emp.editar = true;
        },
        cancelAddEmpresa(){
            this.addEmpresa = false;
        },
        async deleteEmpresa(id) {
            const res = await this.call_api("get", `user/companies/delete/${id}`);
            if (res.data.success) {
                this.getEmpresasUser();
                this.snack({ message: res.data.message });
            }
        },
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