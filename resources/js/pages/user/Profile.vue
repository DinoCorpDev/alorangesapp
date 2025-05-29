<template>
    <div class="px-0 px-sm-5 py-3">
        <!-- <address-dialog
            :typeAddress="typeAddress"
            :show="addDialogShow"
            @close="addressDialogClosed"
            :old-address="addressSelectedForEdit"
        /> -->
        <profile-dialog :show="profileDialogShow" @close="profileDialogClosed" :old-profile="profileSelectedForEdit" />
        <password-dialog
            :show="passwordDialogShow"
            @close="passwordDialogClosed"
            :old-password="passwordSelectedForEdit"
        />
        <factory-dialog :show="factoryDialogShow" @close="factoryDialogClosed" :old-factory="factorySelectedForEdit" />
        <v-row>
            <!-- USUARIO PRINCIPAL---------------------------------------------------------------------->

            <v-col cols="12" md="6">
                <h5 class="fw-600">Información</h5>
                <v-divider class="my-4" />
                <v-card
                    elevation="0"
                    class="mb-6 form-border rounded-lg pa-5"
                    style="background: transparent; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2)"
                    v-if="!editarUser"
                >
                    <v-row>
                        <v-col style="display: flex; place-items: center">
                            <h5 class="fw-600" style="display: inline">Usuario principal</h5>
                        </v-col>

                        <v-col class="text-end" style="display: flex; align-items: center; justify-content: right">
                            <div style="margin-right: 15px; margin-top: 3px">
                                <!-- <v-tooltip bottom color="black">
                                    <template v-slot:activator="{ on, attrs }">
                                        <i class="las la-star" v-bind="attrs" v-on="on" style="font-size: 25px"> </i>
                                    </template>
                                    <span>Favorito</span>
                                </v-tooltip> -->
                            </div>
                            <i
                                class="las la-eye-slash"
                                style="font-size: 25px"
                                v-if="mostrarDatos"
                                @click="ocultarDatosUsuario"
                            ></i>
                            <i class="las la-eye" style="font-size: 25px" @click="mostrarDatosUsuario" v-else></i>
                        </v-col>
                    </v-row>
                    <v-divider class="my-4" />

                    <div v-if="mostrarDatos">
                        <input type="email" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                        <input type="password" name="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Correo electroníco</label>
                            </v-col>

                            <v-col class="text-end usuario-lineado">
                                {{ currentUser.email || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Tipo de persona</label>
                            </v-col>

                            <v-col class="text-end usuario-lineado">
                                {{ currentUser.personType || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Nombre completo</label>
                            </v-col>

                            <v-col class="text-end usuario-lineado">
                                {{ currentUser.name || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Tipo de documento</label>
                            </v-col>

                            <v-col class="text-end usuario-lineado">
                                {{ currentUser.documentType || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Número de documento</label>
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
                            <v-col cols="6" class="text-start usuario-lineado">
                                <label class="profile-label">Correo electroníco</label>
                            </v-col>
                            <v-col cols="6" class="text-end usuario-lineado">******@******.*** </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Tipo de persona</label>
                            </v-col>
                            <v-col class="text-end usuario-lineado"> {{ currentUser.personType || "--" }} </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Nombre completo</label>
                            </v-col>
                            <v-col class="text-end usuario-lineado">***** ***** ******</v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start usuario-lineado">
                                <label class="profile-label">Tipo de documento</label>
                            </v-col>
                            <v-col class="text-end usuario-lineado">
                                {{ currentUser.documentType || "--" }}
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="text-start">
                                <label class="profile-label">Número de documento</label>
                            </v-col>
                            <v-col class="text-end"> *********** </v-col>
                        </v-row>
                    </div>
                    <v-row>
                        <v-col>
                            <custom-button color="white" style="width: 33%" text="Editar" @click="editProfile()" />
                        </v-col>
                    </v-row>
                </v-card>

                <v-card elevation="0" class="mb-6 rounded-lg pa-5" style="background-color: white" v-else>
                    <h5 class="fw-600 mb-4">Editar usuario principal</h5>

                    <v-divider class="mb-4" />

                    <v-form class="inputs" ref="loginForm" lazy-validation>
                        <v-row>
                            <v-col cols="12" md="6" style="display: grid; align-items: end">
                                <span class="black--text body-2">Primer Nombre</span>
                                <custom-input
                                    class="place-holder"
                                    placeholder="Ingresar nombre"
                                    v-model="formUser.firstName"
                                    :error-messages="firstNameErrors"
                                    @blur="$v.formUser.firstName.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2"> Segundo Nombre (Opcional) </span>
                                <custom-input
                                    class="place-holder"
                                    placeholder="Ingresar segundo nombre"
                                    v-model="formUser.secondName"
                                />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2">Primer Apellido</span>
                                <custom-input
                                    class="place-holder"
                                    placeholder="Ingresar primer apellido"
                                    v-model="formUser.firstLastname"
                                    :error-messages="firstLastnameErrors"
                                    @blur="$v.formUser.firstLastname.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <span class="black--text body-2">Segundo Apellido</span>
                                <custom-input
                                    class="place-holder"
                                    placeholder="Ingresar segundo apellido"
                                    v-model="formUser.secondLastname"
                                    :error-messages="secondLastnameErrors"
                                    @blur="$v.formUser.secondLastname.$touch()"
                                    required
                                />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="12">
                                <span class="black--text body-2">Documento</span>
                                <select-custom
                                    placeholder="Seleccionar tipo de documento"
                                    class="select-placeholder"
                                    :items="documentTypes"
                                    v-model="formUser.documentType"
                                    :error-messages="documentTypeErrors"
                                    @blur="$v.formUser.documentType.$touch()"
                                    required
                                />
                            </v-col>
                            <v-col cols="12" md="12">
                                <span class="black--text body-2">Numero de Documento</span>
                                <custom-input
                                    class="place-holder"
                                    placeholder="Ingresar número de documento"
                                    v-model="formUser.documentNumber"
                                    :error-messages="documentNumberErrors"
                                    @blur="$v.formUser.documentNumber.$touch()"
                                    required
                                />
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="12">
                                <span class="black--text body-2">
                                    {{ $t("Contraseña") }}
                                </span>
                                <v-text-field
                                    v-model="formUser.oldPassword"
                                    placeholder="Ingresar contraseña"
                                    :error-messages="oldPasswordErrors"
                                    @blur="$v.formUser.oldPassword.$touch()"
                                    :type="oldpasswordShow ? 'text' : 'password'"
                                    :append-icon="oldpasswordShow ? 'las la-eye' : 'las la-eye-slash'"
                                    class="input-group--focused place-holder"
                                    hide-details="auto"
                                    required
                                    dense
                                    outlined
                                    @click:append="oldpasswordShow = !oldpasswordShow"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <custom-button
                                    block
                                    class="mt-5"
                                    text="< Cancelar"
                                    @click="editarUser = false"
                                    color="white"
                                />
                            </v-col>
                            <v-col></v-col>
                            <v-col cols="4" style="display: grid; align-items: end">
                                <div class="">
                                    <v-dialog v-model="dialog" width="500" class="grey lighten-4 rounded-lg">
                                        <template v-slot:activator="{ on, attrs }">
                                            <custom-button
                                                block
                                                color="orange"
                                                text="Guardar >"
                                                v-bind="attrs"
                                                v-on="on"
                                            />
                                        </template>

                                        <v-card>
                                            <v-card-title class="text-h5 grey lighten-4">
                                                ¿Deseas editar esta información?
                                            </v-card-title>
                                            <div class="grey lighten-4" style="width: 100%">
                                                <v-divider class="mx-5 pb-4" />
                                            </div>
                                            <v-card-text class="grey lighten-4" style="color: black">
                                                Si decides editar esta información podrías perder la garantía a los
                                                productos vinculados a procesos de garantía, servicios y facturaciones.
                                            </v-card-text>

                                            <v-card-actions class="pb-5 grey lighten-4" style="display: block">
                                                <v-spacer></v-spacer>
                                                <custom-button
                                                    text="NO"
                                                    width="100"
                                                    color="white"
                                                    @click="dialog = false"
                                                />
                                                <custom-button
                                                    text="EDITAR"
                                                    width="100"
                                                    color="orange"
                                                    @click.stop="updateInfoUser"
                                                    :disabled="infoUpdateLoading"
                                                    :loading="infoUpdateLoading"
                                                />
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </div>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>
            </v-col>

            <v-col cols="12" md="6" v-if="currentUser.personType !== 'Natural'">
                <h5 class="fw-600">Empresas</h5>
                <v-divider class="my-4" />
                <div v-for="(emp, i) in empresas" :key="i">
                    <div v-if="emp.editar == false">
                        <div v-if="emp.mostrarDatos">
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600" style="display: inline">{{ emp.company_razon }}</h5>
                                <div style="width: auto; display: inline; float: right">
                                    <!-- <v-tooltip bottom color="black">
                                        <template v-slot:activator="{ on, attrs }">
                                            <i
                                                v-if="emp.favorite == 1"
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="font-size: 25px; margin-right: 15px"
                                                v-on:click="offFavoriteEmp(emp)"
                                            >
                                            </i>

                                            <i
                                                v-else
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="color: #cbc2c2f2; font-size: 25px; margin-right: 15px"
                                                v-on:click="activeFavoriteEmp(emp)"
                                            >
                                            </i>
                                        </template>
                                        <span>Favorito</span>
                                    </v-tooltip> -->
                                    <i
                                        class="las la-eye-slash"
                                        style="font-size: 25px"
                                        v-if="emp.mostrarDatos"
                                        @click="ocultarDatosEmpresa(emp)"
                                    ></i>
                                    <i
                                        class="las la-eye"
                                        style="font-size: 25px"
                                        @click="mostrarDatosEmpresa(emp)"
                                        v-else
                                    ></i>
                                </div>
                                <v-divider class="my-4" />

                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Correo electroníco</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">{{ emp.company_razon || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Tipo de persona</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> {{ emp.person_type || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Nombre representante</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">{{
                                        emp.first_name + " " + emp.first_lastname || "--"
                                    }}</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Tipo de documento</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.document_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Número de documento</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.document_number || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Razón social</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.company_razon || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Entidad comercial</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.company_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Número de NIT</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.document_number || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Correo electrónico (facturación)</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.company_email || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start pb-5">
                                        <label class="profile-label">Teléfono/Celular (área contrable)</label>
                                    </v-col>
                                    <v-col class="text-end pb-5">
                                        {{ emp.company_phone || "--" }}
                                    </v-col>
                                </v-row>
                                <custom-button
                                    class="mr-3"
                                    color="white"
                                    style="width: 33%"
                                    text="Editar"
                                    @click="editEmpresa(emp)"
                                />
                            </v-card>
                        </div>

                        <div v-else>
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600" style="display: inline">{{ emp.company_razon }}</h5>
                                <div style="width: auto; display: inline; float: right">
                                    <!-- <v-tooltip bottom color="black">
                                        <template v-slot:activator="{ on, attrs }">
                                            <i
                                                v-if="emp.favorite == 1"
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="font-size: 25px; margin-right: 15px"
                                                v-on:click="offFavoriteEmp(emp)"
                                            >
                                            </i>

                                            <i
                                                v-else
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="color: #cbc2c2f2; font-size: 25px; margin-right: 15px"
                                                v-on:click="activeFavoriteEmp(emp)"
                                            >
                                            </i>
                                        </template>
                                        <span>Favorito</span>
                                    </v-tooltip> -->
                                    <i
                                        class="las la-eye-slash"
                                        v-if="emp.mostrarDatos"
                                        @click="ocultarDatosEmpresa(emp)"
                                        style="font-size: 25px"
                                    ></i>
                                    <i
                                        class="las la-eye"
                                        @click="mostrarDatosEmpresa(emp)"
                                        style="font-size: 25px"
                                        v-else
                                    ></i>
                                </div>
                                <v-divider class="my-4" />
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Correo electrónico</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">******@*******.*****</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Tipo de persona</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> {{ emp.person_type || "--" }} </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Nombre representante</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">********* **********</v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Tipo de documento</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.document_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Número de documento</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> ************ </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Razón social</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> ************** </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Entidad comercial</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado">
                                        {{ emp.company_type || "--" }}
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Número de NIT</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> ************* </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start usuario-lineado">
                                        <label class="profile-label">Correo electrónico (facturación)</label>
                                    </v-col>
                                    <v-col class="text-end usuario-lineado"> *******@*****.**** </v-col>
                                </v-row>
                                <v-row>
                                    <v-col class="text-start pb-5">
                                        <label class="profile-label">Teléfono/celular (área contable)</label>
                                    </v-col>
                                    <v-col class="text-end pb-5"> ********** </v-col>
                                </v-row>

                                <custom-button
                                    class="mr-3"
                                    color="white"
                                    style="width: 33%"
                                    text="Editar"
                                    @click="editEmpresa(emp)"
                                />
                            </v-card>
                        </div>
                    </div>

                    <div v-if="emp?.editar == true">
                        <v-card elevation="0" class="mb-6 rounded-lg pa-5" style="background-color: white">
                            <h5 class="fw-600">Editar Empresa</h5>
                            <v-divider class="ma-4" />
                            <v-form :validator="$v.emp" autocomplete="chrome-off">
                                <v-row>
                                    <v-col cols="12" sm="6" style="display: grid; align-items: end">
                                        <span class="black--text body-2">Primer Nombre *</span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar nombre"
                                            v-model="emp.first_name"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <span class="black--text body-2"> Segundo Nombre (Opcional) </span>
                                        <CustomInput
                                            class="place-holder"
                                            v-model="emp.second_name"
                                            placeholder="Ingresar segundo nombre"
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <span class="black--text body-2"> Primer Apellido *</span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar primer apellido"
                                            v-model="emp.first_lastname"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <span class="black--text body-2"> Segundo Apellido *</span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar segundo apellido"
                                            v-model="emp.second_lastname"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2"> Documento (Representante) *</span>
                                        <SelectCustom
                                            class="select-style"
                                            placeholder="Seleccionar tipo de documento"
                                            :items="documentTypes"
                                            v-model="emp.document_type"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2">Numero de Documento *</span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Ingresar número de documento"
                                            v-model="emp.document_number"
                                            required
                                        />
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2">Razón Social de la Empresa *</span>
                                        <CustomInput
                                            placeholder="Ingrese razón social de la empresa"
                                            class="place-holder"
                                            v-model="emp.company_razon"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2">Entidad Comercial *</span>
                                        <SelectCustom
                                            class="select-style"
                                            placeholder="Seleccione entidad comercial"
                                            :items="companyTypes"
                                            v-model="emp.company_type"
                                            required
                                        />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2">Numero de NIT *</span>
                                        <CustomInput
                                            class="place-holder"
                                            placeholder="Indicar número de documento"
                                            v-model="emp.company_document_number"
                                            required
                                        />
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="12">
                                        <span class="black--text body-2"
                                            >{{ $t("CORREO ELECTRÓNICO O NÚMERO DE TELÉFONO") }} *</span
                                        >

                                        <div class="input-group">
                                            <CustomInput
                                                class="place-holder"
                                                placeholder="Ingresar correo electrónico o teléfono"
                                                type="email"
                                                v-model="emp.company_email"
                                                required
                                            />
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="12">
                                        <span class="black--text body-2">Teléfono / Celular (Área Contable) *</span>
                                        <v-row>
                                            <v-col cols="12">
                                                <vue-tel-input
                                                    placeholder="Ingresar teléfono / celular"
                                                    v-model="emp.company_phone"
                                                    v-bind="mobileInputProps"
                                                    :onlyCountries="availableCountries"
                                                    class="place-holder"
                                                >
                                                    <template slot="arrow-icon">
                                                        <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                    </template>
                                                </vue-tel-input>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="6">
                                        <span class="black--text body-2">
                                            {{ $t("ACTIVIDAD ECONOMICA (CÓDIGO CIIU)") }} *
                                        </span>

                                        <div class="input-group">
                                            <SelectCustom
                                                class="select-style"
                                                placeholder="Seleccione actividad economica"
                                                :items="codigoCiiuTypes"
                                                v-model="emp.companyActividads"
                                                :multiple="true"
                                                required
                                            />
                                        </div>
                                    </v-col>

                                    <v-col cols="12" md="6" style="display: grid; align-items: end">
                                        <span class="black--text body-2">REGIMEN FISCAL *</span>

                                        <div class="input-group">
                                            <SelectCustom
                                                class="select-style"
                                                placeholder="Seleccione regimen fiscal"
                                                :items="regimenTypes"
                                                v-model="emp.regimenFiscals"
                                                :multiple="true"
                                                required
                                            />
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <span class="black--text body-2">RESPONSABILIDAD TRIBUTARIA *</span>
                                        <SelectCustom
                                            class="select-style"
                                            placeholder="Seleccione responsabilidad tributaria"
                                            :items="responsabilidadTypes"
                                            v-model="emp.responsabilidadTributs"
                                            :multiple="true"
                                            required
                                        />
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2"> DOCUMENTO (ARCHIVO) </span>
                                    </v-col>
                                    <v-col cols="10" class="area-upload">
                                        <v-file-input
                                            placeholder="SUBIR DOCUMENTO"
                                            class="form-control upload"
                                            v-model="emp.filedocumento"
                                            accept="application/pdf"
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <p class="subir-documento">SUBIR DOCUMENTO</p>
                                            </template>
                                        </v-file-input>
                                    </v-col>
                                    <v-col cols="2" class="icono-documento">
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
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2"> Numero de CAMARA DE COMERCIO (ARCHIVO) </span>
                                    </v-col>
                                    <v-col cols="10" class="area-upload">
                                        <!-- REVISAR--------------------------------- -->
                                        <v-file-input
                                            class="form-control upload"
                                            v-model="emp.filecamara"
                                            accept="application/pdf"
                                            single-line
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <span class="subir-documento">SUBIR DOCUMENTO</span>
                                            </template>
                                        </v-file-input>

                                        <!-- REVISAR--------------------------------- -->
                                    </v-col>
                                    <v-col cols="2" class="icono-documento">
                                        <!--<i class="las la-exclamation-circle icon-tooltip-dos" style="font-size: 35px; transform: rotate(180deg);" data-title=" - Documentos en formato PDF.\n- RUT actualizado"></i>-->
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
                                            <span>&bullet; Cámara de comercio no mayor a 90 días</span>
                                        </v-tooltip>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="texto-upload">
                                        <span class="black--text body-2"> RUT (ARCHIVO) kjjkbhjbbjhb </span>
                                    </v-col>
                                    <v-col cols="10" class="area-upload">
                                        <v-file-input
                                            placeholder="SUBIR DOCUMENTO"
                                            class="form-control upload"
                                            v-model="emp.filerut"
                                            accept="application/pdf"
                                        >
                                            <template v-slot:label>
                                                <ArrowUpload />
                                                <span class="subir-documento">SUBIR DOCUMENTO</span>
                                            </template>
                                        </v-file-input>
                                    </v-col>
                                    <v-col cols="2" class="icono-documento">
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

                                <v-row>
                                    <v-col cols="4" md="4">
                                        <custom-button
                                            block
                                            class="mt-4"
                                            text="< Cancelar"
                                            type="button"
                                            color="white"
                                            @click="cancelEditEmpresa(emp)"
                                        />
                                    </v-col>
                                    <v-col
                                        cols="4"
                                        md="4"
                                        style="
                                            display: flex;
                                            align-items: end;
                                            justify-content: center;
                                            padding-left: 23px;
                                        "
                                    >
                                        <CustomButtonR
                                            class="mr-3 boton-redondo"
                                            text="Eliminar"
                                            @click="deleteEmpresa(emp?.id)"
                                        />
                                    </v-col>
                                    <v-col cols="4" md="4">
                                        <custom-button
                                            block
                                            class="mt-4"
                                            text="Guardar >"
                                            type="submit"
                                            color="white"
                                            @click="saveEditEmpresa(emp)"
                                            :disabled="infoUpdateLoading"
                                            :loading="infoUpdateLoading"
                                        />
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card>
                    </div>
                </div>

                <div v-if="addEmpresa">
                    <v-card
                        elevation="0"
                        class="mb-6 form-border rounded-lg pa-5"
                        style="background-color: white; border-color: #fafcfc"
                    >
                        <h5 class="fw-600">Añadir Empresa</h5>
                        <v-divider class="my-4" />
                        <v-form :validator="$v.formEmpresa" autocomplete="chrome-off">
                            <v-row>
                                <v-col cols="12" sm="6" style="display: grid; align-items: end">
                                    <span class="black--text body-2">Primer Nombre</span>

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
                                    <span class="black--text body-2">Segundo Nombre (Opcional) </span>
                                    <CustomInput
                                        class="place-holder"
                                        v-model="formEmpresa.secondName"
                                        placeholder="Ingresar segundo nombre"
                                    />
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <span class="black--text body-2">Primer Apellido </span>
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
                                    <span class="black--text body-2">Segundo Apellido </span>
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
                                    <span class="black--text body-2">Documento (Representante) </span>
                                    <SelectCustom
                                        class="select-style"
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
                                    <span class="black--text body-2">Numero de Documento</span>
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
                                    <span class="black--text body-2">Razón Social de la Empresa</span>
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
                                    <span class="black--text body-2">Entidad Comercial</span>
                                    <SelectCustom
                                        class="select-style"
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
                                    <span class="black--text body-2">Numero de NIT </span>
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
                                    <span class="black--text body-2">
                                        {{ $t("Correo electroníco o número de teléfono") }}
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
                                    <span class="black--text body-2">Teléfono / Celular (Área Contable)</span>
                                    <v-row>
                                        <v-col cols="12">
                                            <vue-tel-input
                                                placeholder="Ingresar teléfono / celular"
                                                v-model="formEmpresa.companyPhone"
                                                v-bind="mobileInputProps"
                                                :onlyCountries="availableCountries"
                                                @blur="$v.formEmpresa.companyPhone.$touch()"
                                                :class="{
                                                    'error--text':
                                                        $v.formEmpresa.companyPhone.$error ||
                                                        formEmpresa.showInvalidPhone
                                                }"
                                                class="place-holder"
                                            >
                                                <template slot="arrow-icon">
                                                    <span class="vti__dropdown-arrow">&nbsp;▼</span>
                                                </template>
                                            </vue-tel-input>
                                            <div
                                                class="v-text-field__details mt-2 pl-3"
                                                v-if="$v.formEmpresa.companyPhone.$error"
                                            >
                                                <div class="v-messages theme--light error--text" role="alert">
                                                    <div class="v-messages__wrapper">
                                                        <div class="v-messages__message">
                                                            {{ $t("*Este campo es obligatorio") }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="v-text-field__details mt-2 pl-3"
                                                v-if="
                                                    !$v.formEmpresa.companyPhone.$error && formEmpresa.showInvalidPhone
                                                "
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
                                    <span class="black--text body-2">
                                        {{ $t("ACTIVIDAD ECONOMICA (Código CIIU)") }}
                                    </span>

                                    <div class="input-group">
                                        <SelectCustom
                                            class="select-style"
                                            placeholder="Seleccione actividad economica"
                                            :items="codigoCiiuTypes"
                                            v-model="formEmpresa.companyActividad"
                                            :error-messages="companyActividadErrors"
                                            :multiple="true"
                                            @blur="$v.formEmpresa.companyActividad.$touch()"
                                            required
                                        />
                                    </div>
                                </v-col>

                                <v-col cols="12" md="6" style="display: grid; align-items: end">
                                    <span class="black--text body-2">Regimen fiscal</span>

                                    <div class="input-group">
                                        <SelectCustom
                                            class="select-style"
                                            placeholder="Seleccione regimen fiscal"
                                            :items="regimenTypes"
                                            v-model="formEmpresa.regimenFiscal"
                                            :multiple="true"
                                            :error-messages="regimenFiscalErrors"
                                            @blur="$v.formEmpresa.regimenFiscal.$touch()"
                                            required
                                        />
                                    </div>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12">
                                    <span class="black--text body-2">Responsabilidad tributaria </span>
                                    <SelectCustom
                                        class="select-style"
                                        placeholder="Seleccione responsabilidad tributaria"
                                        :items="responsabilidadTypes"
                                        v-model="formEmpresa.responsabilidadTribut"
                                        :multiple="true"
                                        :error-messages="responsabilidadTributErrors"
                                        @blur="$v.form.responsabilidadTribut.$touch()"
                                        required
                                    />
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2">Documento (ARCHIVO) </span>
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
                                            <span class="subir-documento">Subir documento</span>
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
                                        <span>&bullet; Ambas caras de ID ampliadas al 150</span>
                                        <br />
                                        <span>&bullet; Documento legible y en color</span>
                                    </v-tooltip>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2"> Numero de Camara de comercio (ARCHIVO) </span>
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
                                            <span class="subir-documento">Subir documento</span>
                                        </template>
                                    </v-file-input>

                                    <!-- REVISAR--------------------------------- -->
                                </v-col>
                                <v-col cols="1" class="icono-documento">
                                    <!--<i class="las la-exclamation-circle icon-tooltip-dos" style="font-size: 35px; transform: rotate(180deg);" data-title=" - Documentos en formato PDF.\n- RUT actualizado"></i>-->
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
                                        <span>&bullet; Cámara de comercio no mayor a 90 días</span>
                                    </v-tooltip>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12" class="texto-upload">
                                    <span class="black--text body-2"> RUT (ARCHIVO) </span>
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
                                            <span class="subir-documento">Subir documento</span>
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

                            <v-row>
                                <v-col cols="4" md="4">
                                    <custom-button
                                        block
                                        class="mt-4"
                                        text="< Cancelar"
                                        type="button"
                                        color="white"
                                        @click="cancelAddEmpresa()"
                                    />
                                </v-col>
                                <v-col cols="4" md="4" style="margin-left: 33%">
                                    <custom-button
                                        block
                                        class="mt-4"
                                        text="Guardar >"
                                        type="submit"
                                        color="orange"
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
                    <v-row>
                        <v-col cols="3" style="display: flex; place-items: center">
                            <EmpresasIcon />
                        </v-col>
                        <v-col cols="9" style="display: flex; place-items: center">
                            <p class="mb-0">
                                Realiza las compras como persona juridica agregando los datos de facturación como
                                empresa.
                            </p>
                        </v-col>
                        <CustomButton class="mb-4" color="white" @click="addEmpresa = true" text="Añadir mi Empresa" />
                    </v-row>
                </v-card>
            </v-col>
        </v-row>

        <!-- DIRECCIONES ----------------------------------------------------------------------------->

        <v-row>
            <v-col cols="12" class="pb-0">
                <h5 class="fw-600">Direcciones</h5>
                <v-divider class="my-4" />
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6" class="pt-0" v-for="(otherAdd, i) in otherAdress" :key="i">
                <div>
                    <div v-if="otherAdd?.editar == false">
                        <div v-if="otherAdd.mostrarDatos">
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600 text-capitalize" style="display: inline">{{ otherAdd?.name }}</h5>
                                <div style="width: auto; display: inline; float: right">
                                    <!-- <v-tooltip bottom color="black">
                                        <template v-slot:activator="{ on, attrs }">
                                            <i
                                                v-if="otherAdd.favorite == 1"
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="font-size: 25px; margin-right: 15px"
                                                v-on:click="offFavoriteAddress(otherAdd)"
                                            >
                                            </i>

                                            <i
                                                v-else
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="color: #cbc2c2f2; font-size: 25px; margin-right: 15px"
                                                v-on:click="activeFavoriteAddress(otherAdd)"
                                            >
                                            </i>
                                        </template>
                                        <span>Favorito</span>
                                    </v-tooltip> -->
                                    <i
                                        class="las la-eye-slash"
                                        v-if="otherAdd.mostrarDatos"
                                        @click="ocultarDatosDirection(otherAdd)"
                                        style="font-size: 25px"
                                    ></i>
                                    <i
                                        class="las la-eye"
                                        @click="mostrarDatosDirection(otherAdd)"
                                        style="font-size: 25px"
                                        v-else
                                    ></i>
                                </div>
                                <v-divider class="my-4" />
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">País</span>
                                    <span class="body1 text-right">{{ otherAdd?.country }}</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Dirección</span>
                                    <span class="body1 text-right">{{ otherAdd?.address }}</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Dirección adicional</span>
                                    <span class="body1 text-right">{{ otherAdd?.details }}</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Codigo Postal</span>
                                    <span class="body1">{{ otherAdd?.postal_code }}</span>
                                </div>
                                <!-- localidad -->
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Departamento</span>
                                    <span class="body1">{{ otherAdd?.state }}</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Municipio</span>
                                    <span class="body1">{{ otherAdd?.city }}</span>
                                </div>
                                <!-- <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Localidad</span>
                                    <span class="body1">{{ otherAdd?.localidad }}</span>
                                </div> -->

                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Barrio</span>
                                    <span class="body1">
                                        {{ otherAdd?.neighborhood ? otherAdd?.neighborhood : "--" }}
                                    </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Telefono / Movil</span>
                                    <span class="body1">{{ otherAdd?.phone }}</span>
                                </div>
                                <custom-button
                                    class="mr-3"
                                    color="white"
                                    style="width: 33%"
                                    text="Editar"
                                    @click="editDirection(otherAdd)"
                                />
                            </v-card>
                        </div>
                        <div v-else>
                            <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                                <h5 class="fw-600 text-capitalize" style="display: inline">{{ otherAdd?.name }}</h5>
                                <div style="width: auto; display: inline; float: right">
                                    <!-- <v-tooltip bottom color="black">
                                        <template v-slot:activator="{ on, attrs }">
                                            <i
                                                v-if="otherAdd.favorite == 1"
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="font-size: 25px; margin-right: 15px"
                                                v-on:click="offFavoriteAddress(otherAdd)"
                                            >
                                            </i>

                                            <i
                                                v-else
                                                class="las la-star"
                                                v-bind="attrs"
                                                v-on="on"
                                                style="color: #cbc2c2f2; font-size: 25px; margin-right: 15px"
                                                v-on:click="activeFavoriteAddress(otherAdd)"
                                            >
                                            </i>
                                        </template>
                                        <span>Favorito</span>
                                    </v-tooltip> -->
                                    <i
                                        class="las la-eye-slash"
                                        v-if="otherAdd.mostrarDatos"
                                        @click="ocultarDatosDirection(otherAdd)"
                                        style="font-size: 25px"
                                    ></i>
                                    <i
                                        class="las la-eye"
                                        @click="mostrarDatosDirection(otherAdd)"
                                        style="font-size: 25px"
                                        v-else
                                    ></i>
                                </div>
                                <v-divider class="my-4" />
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">País</span>
                                    <span class="body1 text-right">********</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Dirección</span>
                                    <span class="body1 text-right">******** ***** *****</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Dirección adicional</span>
                                    <span class="body1 text-right">******** *****</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Codigo Postal</span>
                                    <span class="body1">******</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Departamento</span>
                                    <span class="body1">*******</span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Municipio</span>
                                    <span class="body1">*********</span>
                                </div>
                                <!-- <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Localidad</span>
                                    <span class="body1">**********</span>
                                </div> -->
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Barrio</span>
                                    <span class="body1"> *********** </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 fw-600">Telefono / Movil</span>
                                    <span class="body1">**********</span>
                                </div>
                                <custom-button
                                    class="mr-3"
                                    color="white"
                                    style="width: 33%"
                                    text="Editar"
                                    @click="editDirection(otherAdd)"
                                />
                            </v-card>
                        </div>
                    </div>

                    <div v-if="otherAdd?.editar == true">
                        <v-card elevation="0" class="mb-6 rounded-lg pa-5" style="background: white">
                            <h5 class="fw-600">Editar Dirección</h5>
                            <v-divider class="ma-4" />
                            <v-form :validator="$v.otherAdd" autocomplete="chrome-off">
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Nombre de dirección (Casa / oficina)</div>
                                    <CustomInput
                                        v-model="otherAdd.name"
                                        required
                                        class="place-holder"
                                        placeholder="Ingresar casa / oficina"
                                    />
                                </div>

                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Dirección (calle / carrera)</div>
                                    <CustomInput
                                        v-model="otherAdd.address"
                                        required
                                        class="place-holder"
                                        placeholder="Ingresar calle / carrera"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Código postal</div>
                                    <SelectCustom
                                        class="select-style"
                                        placeholder="Seleccione codigo postal"
                                        :items="codigoPostalTypes"
                                        v-model="otherAdd.postal_code"
                                        required
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">País</div>
                                    <SelectCustom
                                        placeholder="Seleccione país"
                                        class="select-style"
                                        :items="countries"
                                        @input="countryChanged"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.country"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Departamento</div>
                                    <SelectCustom
                                        placeholder="Seleccione departamento"
                                        class="select-style"
                                        :items="filteredStates"
                                        @input="stateChanged"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.state"
                                    />
                                </div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Ciudad</div>
                                    <SelectCustom
                                        placeholder="Seleccione ciudad"
                                        class="select-style"
                                        :items="filteredCities"
                                        item-text="name"
                                        item-value="id"
                                        required
                                        v-model="otherAdd.city"
                                    />
                                </div>
                                <v-row>
                                    <!-- <v-col cols="12" sm="6">
                                        <span class="black--text body-2">Localidad</span>
                                        <SelectCustom
                                            placeholder="Seleccione localidad"
                                            class="select-style"
                                            :items="filteredLocalidad"
                                            required
                                            v-model="otherAdd.localidad"
                                        />
                                    </v-col> -->
                                    <v-col cols="12" sm="12">
                                        <div class="mb-1 fs-13 fw-500">Barrio ( Opcional )</div>
                                        <CustomInput
                                            v-model="otherAdd.neighborhood"
                                            class="place-holder"
                                            placeholder="Ingresar barrio"
                                        />
                                    </v-col>
                                </v-row>

                                <div class="mb-3"></div>
                                <div class="mb-3">
                                    <div class="mb-1 fs-13 fw-500">Número de teléfono</div>
                                    <v-row>
                                        <v-col cols="12">
                                            <vue-tel-input
                                                v-model="otherAdd.phone"
                                                v-bind="mobileInputProps"
                                                :onlyCountries="availableCountries"
                                                @validate="phoneValidate"
                                                @blur="$v.formDirection.phone.$touch()"
                                                :class="{
                                                    'error--text':
                                                        $v.formDirection.phone.$error || formDirection.showInvalidPhone
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
                                                            {{ $t("*Este campo es obligatorio") }}
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
                                                color="white"
                                                @click="cancelEditAddress(otherAdd)"
                                            />
                                        </v-col>
                                        <v-col
                                            cols="4"
                                            md="4"
                                            style="display: flex; align-items: end; justify-content: center"
                                        >
                                            <CustomButtonR
                                                class="mr-3 boton-redondo"
                                                text="Eliminar"
                                                @click="deleteAddress(otherAdd?.id)"
                                            />
                                        </v-col>
                                        <v-col cols="4" md="4">
                                            <custom-button
                                                block
                                                class="mt-4"
                                                text="Guardar >"
                                                type="button"
                                                color="orange"
                                                @click="saveEditAddress(otherAdd)"
                                                :disabled="infoUpdateLoading"
                                                :loading="infoUpdateLoading"
                                            />
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-form>
                        </v-card>
                    </div>
                </div>
            </v-col>
            <!-- ----------------------------------------------------------- -->

            <v-col cols="12" md="6" class="pt-0" v-if="addDialogShow">
                <div
                    class="form"
                    style="
                        border: 1px solid #d5d6d9;
                        box-shadow: -2px -2px 5px rgba(0, 0, 0, 0.1), 2px 2px 5px rgba(0, 0, 0, 0.2);
                        border-radius: 10px;
                        padding: 10px;
                    "
                >
                    <AddressDialog
                        :typeAddress="typeAddress"
                        :show="addDialogShow"
                        @close="addressDialogClosed"
                        :old-address="addressSelectedForEdit"
                    />
                </div>
            </v-col>
        </v-row>

        <!-- ------------------------------------- -->
        <v-row>
            <v-col cols="12" md="6">
                <v-card elevation="0" class="mb-6 form-border rounded-lg pa-5">
                    <v-row>
                        <v-col cols="3" style="display: flex; place-items: center">
                            <ProfileAddress />
                        </v-col>

                        <v-col cols="9" style="display: flex; place-items: center">
                            <p class="mb-0">
                                Puedes registrar otras direcciones para los envios de facturacion, entregas de productos
                                y solicitudes de servicio.
                            </p>
                        </v-col>
                        <CustomButton
                            v-if="!addDialogShow"
                            class="mb-4"
                            color="white"
                            text="Añadir Dirección"
                            @click="openAdress('shipping')"
                        />
                    </v-row>
                </v-card>
            </v-col>
        </v-row>

        <!-- CAMBIAR CONTRASEÑA ---------------------------------------------------------------------->

        <v-row>
            <v-col cols="12" md="6">
                <h5 class="fw-600" style="width: 100%">Contraseña</h5>
                <v-divider class="my-4" />
                <v-row v-if="changePassword" class="pt-3" style="display: flex; justify-content: center">
                    <v-card elevation="0" class="mb-6 rounded-lg py-1 pb-5 px-5" style="width: 95%; background: white">
                        <v-col cols="12" class="pb-0">
                            <h5 class="fw-600">Cambiar contraseña</h5>
                            <v-divider class="my-4" />
                        </v-col>
                        <v-col cols="12" md="12" class="pt-0">
                            <span class="black--text body-2">Contraseña actual </span>
                            <v-text-field
                                v-model="formContrasena.oldPassword"
                                placeholder="Ingresar contraseña"
                                :error-messages="olddPasswordErrors"
                                @blur="$v.formContrasena.oldPassword.$touch()"
                                :type="passwordOldShow ? 'text' : 'password'"
                                :append-icon="passwordOldShow ? 'las la-eye' : 'las la-eye-slash'"
                                class="input-group--focused place-holder borde-none"
                                hide-details="auto"
                                required
                                dense
                                outlined
                                @click:append="passwordOldShow = !passwordOldShow"
                                style="background: #f5f5f5"
                            ></v-text-field>
                            <!-- <CustomInput
                                v-model="formContrasena.oldPassword"
                                placeholder="Ingresar contraseña"
                                :error-messages="olddPasswordErrors"
                                @blur="$v.formContrasena.oldPassword.$touch()"
                                :type="passwordOldShow ? 'text' : 'password'"
                                :append-icon="passwordOldShow ? 'las la-eye' : 'las la-eye-slash'"
                                class="input-group--focused place-holder"
                                hide-details="auto"
                                required
                                dense
                                outlined
                                @click:append="passwordOldShow = !passwordOldShow"
                                style="background: #f5f5f5"
                            /> -->
                        </v-col>
                        <v-col cols="12" md="12">
                            <span class="black--text body-2">Nueva contraseña </span>
                            <v-text-field
                                v-model="formContrasena.newPassword"
                                placeholder="Ingresar contraseña"
                                :error-messages="newPasswordErrors"
                                @blur="$v.formContrasena.newPassword.$touch()"
                                :type="passwordNewShow ? 'text' : 'password'"
                                :append-icon="passwordNewShow ? 'las la-eye' : 'las la-eye-slash'"
                                class="input-group--focused place-holder borde-none"
                                hide-details="auto"
                                required
                                dense
                                outlined
                                @click:append="passwordNewShow = !passwordNewShow"
                                style="background: #f5f5f5"
                            >
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" md="12">
                            <span class="black--text body-2">Repetir nueva contraseña </span>
                            <v-text-field
                                v-model="formContrasena.reptyPassword"
                                placeholder="Ingresar contraseña"
                                :error-messages="reptyPasswordErrors"
                                @blur="$v.formContrasena.reptyPassword.$touch()"
                                :type="passwordReptyShow ? 'text' : 'password'"
                                :append-icon="passwordReptyShow ? 'las la-eye' : 'las la-eye-slash'"
                                class="input-group--focused place-holder borde-none"
                                hide-details="auto"
                                required
                                dense
                                outlined
                                @click:append="passwordReptyShow = !passwordReptyShow"
                                style="background: #f5f5f5"
                            ></v-text-field>
                        </v-col>

                        <v-row>
                            <v-col cols="4" md="4">
                                <custom-button
                                    block
                                    class="mt-4"
                                    text="< Cancelar"
                                    type="button"
                                    color="white"
                                    @click="cancelChangePassword()"
                                />
                            </v-col>
                            <v-col cols="4" md="4" style="margin-left: 33%">
                                <custom-button
                                    block
                                    class="mt-4"
                                    text="Guardar >"
                                    type="submit"
                                    color="orange"
                                    @click="saveChangePassword()"
                                    :disabled="infoUpdateLoading"
                                    :loading="infoUpdateLoading"
                                />
                            </v-col>
                        </v-row>
                    </v-card>
                </v-row>

                <v-card
                    elevation="0"
                    class="mb-6 rounded-lg pa-5"
                    style="background-color: transparent"
                    v-if="!changePassword"
                >
                    <CustomButton block color="white" text="Cambiar Contraseña" @click="cambiarContrasena()" />
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { required, requiredIf, email, minLength, sameAs, helpers } from "vuelidate/lib/validators";
import { mapActions, mapGetters, mapMutations } from "vuex";
import CustomButton from "../../components/global/CustomButton.vue";
import { VueTelInput } from "vue-tel-input";
import AddressDialog from "../../components/address/AddressDialog.vue";
import ProfileDialog from "../../components/user/ProfileDialog.vue";
import PasswordDialog from "../../components/user/PasswordDialog.vue";
import FactoryDialog from "../../components/user/FactoryDialog.vue";
import store from "../../store/store";
import CustomButtonR from "../../components/global/CustomButtonRegister.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import CustomCheckbox from "../../components/global/CustomCheckbox.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import ArrowUpload from "../../components/icons/ArrowUpload.vue";
import ProfileAddress from "../../components/icons/ProfileAddress.vue";
import EmpresasIcon from "../../components/icons/EmpresasIcon.vue";

const passwordStrong = helpers.regex(
    "passwordStrong",
    /(?=(.*[0-9]))(?=.*[\!@#$%^&*()\\[\]{}\-_+=|:;"'<>,./?])(?=.*[a-z])(?=(.*[A-Z]))(?=(.*)).{8,}/
);

export default {
    data() {
        return {
            dialog: false
        };
    },
    data: () => ({
        dialog: false,
        mobileInputProps: {
            inputOptions: {
                type: "tel",
                placeholder: "Ingresar teléfono / celular"
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
        // filteredLocalidad: [],
        changePassword: false,
        oldpasswordShow: false,
        passwordOldShow: false,
        passwordNewShow: false,
        passwordReptyShow: false,
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
        responsabilidadTypes: [
            { value: "01", text: "01-IVA" },
            { value: "02", text: "02-IC" },
            { value: "03", text: "03-ICA" },
            { value: "04", text: "04-INC" },
            { value: "05", text: "05-ReteIVA" },
            { value: "06", text: "06-ReteFuente" },
            { value: "07", text: "07-ReteICA" },
            { value: "20", text: "20-FtoHorticultura" },
            { value: "21", text: "21-Timbre" },
            { value: "22", text: "22-Bolsas" },
            { value: "23", text: "23-INCarbono" },
            { value: "24", text: "24-INCombustibles" },
            { value: "25", text: "25-Sobretasa Combustibles" },
            { value: "26", text: "26-Sordicom" },
            { value: "ZY", text: "ZY-No causa (cuando se selecciona esta opción no puede ir otras de las demás)" },
            { value: "ZZ", text: "ZZ-Nombre de la figura tributaria" },
            { value: "48", text: "48-Responsable del Impuesto sobre las ventas - IVA" },
            { value: "49", text: "49-No responsable de IVA" }
        ],
        regimenTypes: [
            { value: "O-13", text: "O-13" },
            { value: "O-15", text: "O-15" },
            { value: "O-23", text: "O-23" },
            { value: "O-47", text: "O-47" },
            { value: "R-99", text: "R-99" }
        ],
        codigoCiiuTypes: [],
        codigoPostalTypes: [],
        companyTypes: [
            { text: "(S.A.S) Sociedad por acciones simplificadas", value: "S.A.S" },
            { text: "(S.A) Sociedad Anónima", value: "S.A" },
            { text: "LTDA Sociedad Limitada", value: "LTDA" },
            { text: "S.C Sociedad Colectiva", value: "S.C" },
            { text: "S.C.A Sociedad comanditaria & Cia", value: "S.C.A" }
        ],
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
            // localidad: "",
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
            regimenFiscal: "",
            responsabilidadTribut: "",
            companyPhone: "",
            companyEmail: "",
            filedocumento: [],
            filecamara: [],
            filerut: []
        },
        formContrasena: {
            oldPassword: "",
            newPassword: "",
            reptyPassword: ""
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
        ArrowUpload,
        ProfileAddress,
        EmpresasIcon,
        CustomButtonR
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
            // localidad: { required },
            phone: { required }
        },
        formEmpresa: {
            firstName: { required },
            firstLastname: { required },
            secondLastname: { required },
            documentType: { required },
            documentNumber: { required },
            companyRazon: { required },
            companyType: { required },
            companyDocumentNumber: { required },
            companyActividad: { required },
            regimenFiscal: { required },
            responsabilidadTribut: { required },
            companyPhone: { required },
            companyEmail: { required }
        },
        formContrasena: {
            oldPassword: { required, minLength: minLength(6), passwordStrong },
            newPassword: { required, minLength: minLength(6), passwordStrong },
            reptyPassword: { required, sameAsPassword: sameAs("newPassword") }
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
            !this.$v.form.name.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.form.email.$dirty) return errors;
            !this.$v.form.email.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            !this.$v.form.email.email && errors.push(this.$i18n.t("*Ingrese un email válido"));
            return errors;
        },
        oldPasswordErrors() {
            const errors = [];
            if (!this.$v.form.oldPassword.$dirty) return errors;
            !this.$v.form.oldPassword.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.form.password.$dirty) return errors;
            !this.$v.form.password.minLength &&
                errors.push(this.$i18n.t("*La constraseña debe tener mínimo 6 carácteres"));
            return errors;
        },
        confirmPasswordErrors() {
            const errors = [];
            if (!this.$v.form.confirmPassword.$dirty) return errors;
            !this.$v.form.confirmPassword.sameAsPassword && errors.push(this.$i18n.t("*Las constraseñas no coinciden"));
            return errors;
        },
        firstNameErrors() {
            const errors = [];
            if (!this.$v.formUser.firstName.$dirty) return errors;
            !this.$v.formUser.firstName.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        secondNameErrors() {
            const errors = [];
            if (!this.$v.formUser.secondName.$dirty) return errors;
            !this.$v.formUser.secondName.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        firstLastnameErrors() {
            const errors = [];
            if (!this.$v.formUser.firstLastname.$dirty) return errors;
            !this.$v.formUser.firstLastname.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        secondLastnameErrors() {
            const errors = [];
            if (!this.$v.formUser.secondLastname.$dirty) return errors;
            !this.$v.formUser.secondLastname.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        documentTypeErrors() {
            const errors = [];
            if (!this.$v.formUser.documentType.$dirty) return errors;
            !this.$v.formUser.documentType.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        documentNumberErrors() {
            const errors = [];
            if (!this.$v.formUser.documentNumber.$dirty) return errors;
            !this.$v.formUser.documentNumber.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        addressErrors() {
            const errors = [];
            if (!this.$v.formDirection.address.$dirty) {
                return errors;
            }
            !this.$v.formDirection.address.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        addressNameErrors() {
            const errors = [];
            if (!this.$v.formDirection.name.$dirty) {
                return errors;
            }
            !this.$v.formDirection.name.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        addressDetailsErrors() {
            const errors = [];
            if (!this.$v.formDirection.details.$dirty) {
                return errors;
            }
            !this.$v.formDirection.details.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        postalCodeErrors() {
            const errors = [];
            if (!this.$v.formDirection.postal_code.$dirty) {
                return errors;
            }
            !this.$v.formDirection.postal_code.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        countryErrors() {
            const errors = [];
            if (!this.$v.formDirection.country.$dirty) {
                return errors;
            }
            !this.$v.formDirection.country.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        stateErrors() {
            const errors = [];
            if (!this.$v.formDirection.state.$dirty) {
                return errors;
            }
            !this.$v.formDirection.state.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        cityErrors() {
            const errors = [];
            if (!this.$v.formDirection.city.$dirty) {
                return errors;
            }
            !this.$v.formDirection.city.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        // localidadErrors() {
        //     const errors = [];
        //     if (!this.$v.formDirection.localidad.$dirty) return errors;
        //     !this.$v.formDirection.localidad.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
        //     return errors;
        // },
        phoneErrors() {
            const errors = [];
            if (!this.$v.formDirection.phone.$dirty) {
                return errors;
            }
            !this.$v.formDirection.phone.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyFirstNameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.firstName.$dirty) return errors;
            !this.$v.formEmpresa.firstName.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companySecondNameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.secondName.$dirty) return errors;
            !this.$v.formEmpresa.secondName.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyFirstLastnameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.firstLastname.$dirty) return errors;
            !this.$v.formEmpresa.firstLastname.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companySecondLastnameErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.secondLastname.$dirty) return errors;
            !this.$v.formEmpresa.secondLastname.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyPersonDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.documentType.$dirty) return errors;
            !this.$v.formEmpresa.documentType.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyPersonDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.documentNumber.$dirty) return errors;
            !this.$v.formEmpresa.documentNumber.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyRazonErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyRazon.$dirty) return errors;
            !this.$v.formEmpresa.companyRazon.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyType.$dirty) return errors;
            !this.$v.formEmpresa.companyType.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyDocumentTypeErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyDocumentType.$dirty) return errors;
            !this.$v.formEmpresa.companyDocumentType.required &&
                errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyDocumentNumberErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyDocumentNumber.$dirty) return errors;
            !this.$v.formEmpresa.companyDocumentNumber.required &&
                errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyActividadErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyActividad.$dirty) return errors;
            !this.$v.formEmpresa.companyActividad.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        regimenFiscalErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.regimenFiscal.$dirty) return errors;
            !this.$v.formEmpresa.regimenFiscal.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        responsabilidadTributErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.responsabilidadTribut.$dirty) return errors;
            !this.$v.formEmpresa.responsabilidadTribut.required &&
                errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        companyEmailErrors() {
            const errors = [];
            if (!this.$v.formEmpresa.companyEmail.$dirty) return errors;
            !this.$v.formEmpresa.companyEmail.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            return errors;
        },
        olddPasswordErrors() {
            const errors = [];
            if (!this.$v.formContrasena.oldPassword.$dirty) return errors;
            !this.$v.formContrasena.oldPassword.required && errors.push(this.$i18n.t("this_field_is_required"));
            return errors;
        },
        newPasswordErrors() {
            const errors = [];
            if (!this.$v.formContrasena.newPassword.$dirty) return errors;
            !this.$v.formContrasena.newPassword.required && errors.push(this.$i18n.t("this_field_is_required"));
            !this.$v.formContrasena.newPassword.minLength &&
                errors.push(this.$i18n.t("*La contraseña debe tener mínimo 6 carácteres"));
            !this.$v.formContrasena.newPassword.passwordStrong &&
                errors.push("*La contraseña necesita al menos 1 caracter especial, 1 mayúscula y 1 minúscula");
            return errors;
        },
        reptyPasswordErrors() {
            const errors = [];
            if (!this.$v.formContrasena.reptyPassword.$dirty) return errors;
            !this.$v.formContrasena.reptyPassword.required && errors.push(this.$i18n.t("*Este campo es obligatorio"));
            !this.$v.formContrasena.reptyPassword.sameAsPassword &&
                errors.push(this.$i18n.t("*Las contraseñas no coinciden"));
            return errors;
        }
    },
    async created() {
        this.fetchCountries();
        this.fetchCodigoCiiu();
        this.fetchCodigoPostal();
        await this.getUser();
        this.getAddressUser();
        this.getEmpresasUser();
        this.changeAvatarLoading();

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
        changeAvatarLoading() {
            this.form.previewAvatar = this.currentUser.avatar;
            this.setPreviewAvatar(this.currentUser.avatar);
        },
        cambiarContrasena() {
            this.changePassword = true;
        },
        cancelChangePassword() {
            this.changePassword = false;
        },
        async saveChangePassword() {
            this.$v.formContrasena.$touch();

            this.infoUpdateLoading = true;

            const res = await this.call_api("post", "user/info/updatePassword", this.formContrasena);

            if (res.data.success) {
                this.setUser(res.data.user);
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: res.data.message, color: "red" });
                this.infoUpdateLoading = false;
                return;
            }

            this.infoUpdateLoading = false;
            this.changePassword = false;
        },
        async previewThumbnail(event) {
            this.form.avatar = event.target.files[0];
            if (event.target.files && event.target.files[0]) {
                var form_data = new FormData();
                form_data.append("id", this.currentUser.id);
                form_data.append("avatar", event.target.files[0]);

                const res = await this.call_api("post", "user/info/updateAvatar", form_data, true);

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
        async getEmpresasUser() {
            const res = await this.call_api("get", `user/companies`);
            if (res.data.success) {
                this.empresas = res.data.data;
            }
        },
        mostrarDatosUsuario() {
            this.mostrarDatos = true;
        },
        ocultarDatosUsuario() {
            this.mostrarDatos = false;
        },
        async updateInfoUser() {
            this.$v.formUser.$touch();

            if (this.$v.formUser.$anyError) {
                return;
            }

            this.infoUpdateLoading = true;
            const res = await this.call_api("post", "user/info/update", {
                ...this.formUser,
                password: this.formUser.oldPassword
            });

            if (res.data.success) {
                this.setUser(res.data.user);
                this.dialog = false;
                this.snack({ message: res.data.message });
            } else {
                this.snack({ message: "Ingrese una contraseña válida", color: "red" });
            }

            this.editarUser = false;
            this.infoUpdateLoading = false;
            this.formUser.oldPassword = "";
        },
        async saveAddress() {
            this.$v.formDirection.$touch();

            if (this.$v.formDirection.$anyError) {
                return;
            }

            this.formDirection.phone = this.formDirection.phone.replace(/\s/g, "");

            this.adding = true;
            let data = {
                type: "billing",
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
            this.formUser.secondName = this.currentUser.secondName;
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
        addAddress() {
            this.addDirection = true;
        },
        cancelAddAddress() {
            this.addDirection = false;
        },
        async editDirection(direction) {
            direction.country = direction.country_id;
            let country = await this.countryChanged(direction.country_id);
            direction.state = direction.state_id;

            let state = await this.stateChanged(direction.state_id);
            direction.city = direction.city_id;
            // direction.localidad = direction.localidad_id;
            direction.postal_code = parseInt(direction.postal_code);
            direction.editar = true;
        },
        cancelEditAddress(direction) {
            direction.editar = false;
        },
        async saveEditAddress(direction) {
            this.adding = true;

            if (
                direction.name == "" ||
                direction.address == "" ||
                direction.postal_code == "" ||
                direction.country == "" ||
                direction.state == "" ||
                direction.city == "" ||
                direction.phone == ""
            ) {
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
        async fetchCodigoPostal() {
            const res = await this.call_api("get", "all-codigo-postal");
            if (res.data.success) {
                this.codigoPostalTypes = res.data.data;
            }
        },
        async fetchCodigoCiiu() {
            const res = await this.call_api("get", "all-codigo-ciiu");

            if (res.data.success) {
                this.codigoCiiuTypes = res.data.data;
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
        mostrarDatosDirection(direction) {
            direction.mostrarDatos = true;
        },
        ocultarDatosDirection(direction) {
            direction.mostrarDatos = false;
        },
        mostrarDatosEmpresa(emp) {
            emp.mostrarDatos = true;
        },
        ocultarDatosEmpresa(emp) {
            emp.mostrarDatos = false;
        },

        async saveAddEmpresa() {
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
        cancelAddEmpresa() {
            this.addEmpresa = false;
        },
        editEmpresa(emp) {
            emp.companyActividads = emp.company_actividad;
            emp.regimenFiscals = emp.regimen_fiscal;
            emp.responsabilidadTributs = emp.responsabilidad_tribut;

            emp.editar = true;
        },
        cancelEditEmpresa(emp) {
            emp.editar = false;
        },

        async saveEditEmpresa(emp) {
            this.infoUpdateLoading = true;

            if (
                emp.first_name == "" ||
                emp.first_lastname == "" ||
                emp.second_lastname == "" ||
                emp.document_number == "" ||
                emp.company_razon == "" ||
                emp.company_type == "" ||
                emp.company_document_number == "" ||
                emp.company_email == "" ||
                emp.company_phone == "" ||
                emp.companyActividads == "" ||
                emp.regimenFiscals == "" ||
                emp.responsabilidadTributs == ""
            ) {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
                this.infoUpdateLoading = false;

                return;
            }

            emp.company_actividad = emp.companyActividads;
            emp.regimen_fiscal = emp.regimenFiscals;
            emp.responsabilidad_tribut = emp.responsabilidadTributs;

            const res = await this.call_api("post", `user/companies/update`, emp);

            if (res.data.success) {
                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
            this.infoUpdateLoading = false;
            this.getEmpresasUser();
            emp.editar = false;
        },
        async deleteEmpresa(id) {
            const res = await this.call_api("get", `user/companies/delete/${id}`);
            if (res.data.success) {
                this.getEmpresasUser();
                this.snack({ message: res.data.message });
            }
        },
        async activeFavoriteEmp(emp) {
            emp.favorite = 1;

            const res = await this.call_api("post", `user/companies/setFavorite`, emp);

            if (res.data.success) {
                this.getEmpresasUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async offFavoriteEmp(emp) {
            emp.favorite = 0;

            const res = await this.call_api("post", `user/companies/setFavorite`, emp);

            if (res.data.success) {
                this.getEmpresasUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async activeFavoriteAddress(address) {
            address.favorite = 1;

            const res = await this.call_api("post", `user/address/setFavorite`, address);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async offFavoriteAddress(address) {
            address.favorite = 0;

            const res = await this.call_api("post", `user/address/setFavorite`, address);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async offersConsent() {
            if (this.currentUser.offersConsent) this.currentUser.offersConsent = 0;
            else this.currentUser.offersConsent = 1;

            const res = await this.call_api("post", "user/info/updateTerms", this.currentUser);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async policiesAndCookiesConsent() {
            if (this.currentUser.policiesAndCookiesConsent) this.currentUser.policiesAndCookiesConsent = 0;
            else this.currentUser.policiesAndCookiesConsent = 1;

            const res = await this.call_api("post", "user/info/updateTerms", this.currentUser);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async termsCondTrade() {
            if (this.currentUser.termsCondTrade) this.currentUser.termsCondTrade = 0;
            else this.currentUser.termsCondTrade = 1;

            const res = await this.call_api("post", "user/info/updateTerms", this.currentUser);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async guaranteePolicies() {
            if (this.currentUser.guaranteePolicies) this.currentUser.guaranteePolicies = 0;
            else this.currentUser.guaranteePolicies = 1;

            const res = await this.call_api("post", "user/info/updateTerms", this.currentUser);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
        },
        async termsLogistics() {
            if (this.currentUser.termsLogistics) this.currentUser.termsLogistics = 0;
            else this.currentUser.termsLogistics = 1;

            const res = await this.call_api("post", "user/info/updateTerms", this.currentUser);

            if (res.data.success) {
                this.getAddressUser();

                this.snack({ message: res.data.message });
            } else {
                this.snack({
                    message: this.$i18n.t("something_went_wrong"),
                    color: "red"
                });
            }
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
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2) !important;
    background-color: transparent;
}

.profile {
    &-label {
        text-transform: initial;
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

.place-holder {
    &::v-deep {
        ::placeholder {
            visibility: hidden;
        }

        :hover::placeholder {
            visibility: visible;
            font-style: italic;
        }
    }
}

.usuario-lineado {
    padding-bottom: 0px;
    padding-top: 8px;
}

.upload {
    border: 1px solid #dfdfdf;
    border-radius: 100px;
    width: 100%;
    padding: 0;

    &::v-deep {
        .v-text-field__details {
            display: none !important;
        }
        .v-input__prepend-outer {
            display: none !important;
        }
        .v-label {
            top: 38% !important;
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

.upload:hover {
    animation: upload-animation 0.3s forwards;
    .subir-documento {
        color: white;
    }
}

@keyframes upload-animation {
    0% {
        background: #dfdfdf;
    }

    50% {
        background: black;
    }

    100% {
        background: #858585;
    }
}

.theme--light {
    .v-text-field:not(.v-input--has-state) {
        &::v-deep {
            .v-input__control {
                .v-input__slot {
                    &::before {
                        display: none;
                    }
                }
            }
            &:hover {
                .v-input__control {
                    .v-input__slot {
                        &::before {
                            display: none;
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
    place-items: center;
}
.select-style {
    font-style: italic;
}

.boton-guardar {
    font-size: var(--font-size-btn);
    font-weight: 600;
    letter-spacing: 2;
    transition: all 0.2s ease-in-out;
    color: black;
    background-color: #dfdfdf;

    &:hover {
        color: #fff;
        background-color: rgba(#000000, 0.8);
    }

    &:focus,
    &.v-btn--active {
        color: #fff;
        background-color: #000000;
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
.checkbox-profile {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 24px;
    width: 30px;
    margin-left: 5px;
    margin-right: 30px;
    border-radius: 10px;
    border: 1px solid #000000;
    background-color: #f5f5f5;
}
.checkbox-profile:checked {
    accent-color: black;
}

.custom-checkbox {
    display: flex;
    align-items: center;
    position: relative;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    min-height: 38px;
}

.custom-checkbox label {
    font-family: "Roboto", sans-serif;
    font-size: var(--font-size-body1);
    cursor: pointer;
}

.custom-checkbox label a {
    text-decoration: underline;
    font-weight: 700;
}

.borde-none {
    ::v-deep {
        .v-input__control {
            .v-input__slot {
                fieldset {
                    border: none !important;
                }
            }
        }
    }
}
</style>
