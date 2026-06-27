<template>
    <div>
        <v-stepper elevation="0" v-model="step">
        <v-stepper-header class="mb-3">
            <v-stepper-step step="1">
                <StepOrder text="Lista de Pedido" short-text="Pedido" active first />
            </v-stepper-step>

            <v-stepper-step step="2">
                <StepOrder text="Datos" :active="step >= 2" />
            </v-stepper-step>

            <v-stepper-step step="3">
                <StepOrder text="Pago" :active="step >= 3" />
            </v-stepper-step>

            <v-stepper-step step="4">
                <StepOrder text="Resumen" :active="step >= 4" last />
            </v-stepper-step>
        </v-stepper-header>
        <v-divider class="mb-3" />
        <v-stepper-items>
            <v-stepper-content step="1">
                <template v-if="cartProducts.length > 0">
                    <div class="cart-table-header mb-2">
                        <div>Productos</div>
                        <div>Precio</div>
                        <div>Cantidad</div>
                        <div>Opciones</div>
                    </div>
                    <v-row no-gutters class="car-items">
                        <v-col cols="12" v-for="(product, i) in cartProducts" :key="i">
                            <ProductCart :productDetails="product" productCartType="checkout" />
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" class="d-flex justify-space-between">
                            <div class="mb-2"></div>
                            <total :total="priceTotal" />
                            <div class="mb-2">
                                <CustomButton text="Continuar" color="nero" @click="step = 2" />
                            </div>
                        </v-col>
                    </v-row>
                </template>
                <v-row v-else>
                    <v-col>
                        <div class="emptycart">
                            <div class="cuadro-emptycart">
                                <v-img class="img-cartempty mb-6" src="/public/assets/img/iconoCarrito.png" />
                                <p class="text-cartempty">AUN NO HAY PRODUCTOS EN LA LISTA DE PEDIDOS</p>
                                <CustomButton text="IR A PRODUCTOS" color="nero" class="mt-2" :to="{ name: 'Shop' }" />
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-stepper-content>
            <v-stepper-content step="2">
                <v-row>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Facturar a</h6>
                                    <v-divider class="my-3" />
                                    <SelectCustom dark label="Usuario Principal" :items="langSelectItems" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Correo electronico</span>
                                        <span class="body1">{{ currentUser.email || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Tipo de Persona</span>
                                        <span class="body1">{{ currentUser.personType || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Nombre</span>
                                        <span class="body1">{{ currentUser.name || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-3">
                                        <span class="subtitle1 text-uppercase bold">Documento</span>
                                        <span class="body1">
                                            {{ currentUser.documentType || "--" }}
                                            {{ currentUser.documentNumber || "--" }}
                                        </span>
                                    </div>
                                    <CustomButton color="grey" class="mr-3" text="Editar" @click="editProfile()" />
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de servicio</h6>
                                    <v-divider class="my-3" />
                                    <div
                                        v-if="
                                            Object.entries(addressServicio).length !== 0 && useDefaultAddress1 == false
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{ addressServicio?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressServicio?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressServicio?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressServicio?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressServicio?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>
                                    <label class="label my-3">
                                        <input type="checkbox" v-model="useDefaultAddress1" id="useDefaultAddress1" />
                                        <span class="body-1 black--text text">
                                            Usar la misma Dirección de envió para que Idovela preste los servicio de
                                            instalación, mantenimiento y más.
                                        </span>
                                        <span class="checkmark"></span>
                                    </label>
                                    <v-divider class="my-3" />
                                    <CustomButton
                                        v-if="
                                            Object.entries(addressServicio).length !== 0 && useDefaultAddress1 == false
                                        "
                                        class="mr-3"
                                        color="grey"
                                        text="Editar"
                                        @click="editAddress(addressServicio, 'service')"
                                    />
                                    <CustomButton
                                        v-if="
                                            Object.entries(addressServicio).length === 0 && useDefaultAddress1 == false
                                        "
                                        block
                                        color="grey"
                                        text="Añadir Dirección"
                                        @click="openAdress('service')"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de facturacion</h6>
                                    <v-divider class="my-3" />
                                    <div
                                        v-if="
                                            Object.entries(addressFacturacion).length !== 0 &&
                                            useDefaultAddress2 == false
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{ addressFacturacion?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressFacturacion?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressFacturacion?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressFacturacion?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressFacturacion?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>
                                    <label class="label my-3">
                                        <input type="checkbox" v-model="useDefaultAddress2" id="useDefaultAddress2" />
                                        <span class="body-1 black--text text">
                                            Usar la misma Dirección de envió para que Idovela entreguela factura física.
                                        </span>
                                        <span class="checkmark"></span>
                                    </label>
                                    <v-divider class="my-3" />
                                    <CustomButton
                                        v-if="
                                            Object.entries(addressFacturacion).length !== 0 &&
                                            useDefaultAddress2 == false
                                        "
                                        class="mr-3"
                                        color="grey"
                                        text="Editar"
                                        @click="editAddress(addressFacturacion, 'billing')"
                                    />
                                    <CustomButton
                                        v-if="
                                            Object.entries(addressFacturacion).length === 0 &&
                                            useDefaultAddress2 == false
                                        "
                                        block
                                        color="grey"
                                        text="Añadir Dirección"
                                        @click="openAdress('billing')"
                                    />
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6">
                        <AddressDialog
                            :typeAddress="typeAddress"
                            :show="addDialogShow"
                            @close="addressDialogClosed"
                            :old-address="addressSelectedForEdit"
                        />
                        <ProfileDialog
                            :show="profileDialogShow"
                            @close="profileDialogClosed"
                            :old-profile="profileSelectedForEdit"
                        />
                        <v-row>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de envio</h6>
                                    <v-divider class="my-3" />
                                    <SelectCustom dark label="Usuario Principal" :items="langSelectItems" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                        <span class="body1">Dirección principal</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Dirección</span>
                                        <span class="body1">{{ addressPrincipal?.address }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                        <span class="body1">{{ addressPrincipal?.address }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                        <span class="body1">{{ addressPrincipal?.postal_code }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Departamento</span>
                                        <span class="body1">{{ addressPrincipal?.country }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Municipio</span>
                                        <span class="body1">{{ addressPrincipal?.city }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Barrio</span>
                                        <span class="body1"> -- </span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">
                                            Nombre de quien lo va a recibir
                                        </span>
                                        <span class="body1">{{ addressPrincipal?.name }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                        <span class="body1">{{ addressPrincipal?.phone }}</span>
                                    </div>
                                    <CustomButton
                                        v-if="Object.entries(addressPrincipal).length !== 0"
                                        class="mr-3"
                                        color="grey"
                                        text="Editar"
                                        @click="editAddress(addressPrincipal, 'shipping')"
                                    />
                                    <CustomButton
                                        v-if="Object.entries(addressPrincipal).length === 0"
                                        block
                                        color="grey"
                                        text="Añadir Dirección"
                                        @click="openAdress('shipping')"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Costo logístico</h6>
                                    <v-divider class="my-3" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">
                                            Fleta adicional de envio a todo el país
                                        </span>
                                        <span class="body1">+000.000.000 COP</span>
                                    </div>
                                    <div class="d-flex justify-end mb-2">
                                        <span class="body2 text-uppercase grey--text">
                                            ( Envió sin costos desde 500.000 COP )
                                        </span>
                                    </div>
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" class="d-flex justify-space-between">
                        <div class="mb-2">
                            <CustomButton text="Volver" color="nero" @click="step = 1" />
                        </div>
                        <total :total="priceTotal" />
                        <div class="mb-2">
                            <CustomButton text="Continuar" color="nero" @click="step = 3" />
                        </div>
                    </v-col>
                </v-row>
            </v-stepper-content>
            <v-stepper-content step="3">
                <v-row>
                    <v-col cols="12" md="6" order="1" order-md="1" order-sm="2">
                        <div class="form">
                            <h6 class="black--text bold">Medio de Pago</h6>
                            <v-divider class="my-3" />
                            <v-row>
                                <v-col cols="3">
                                    <TypePayment img="/public/assets/img/pse.png" text="Pse" />
                                    <input type="radio" v-model="pick" :value="1" />
                                </v-col>
                                <v-col cols="3">
                                    <TypePayment img="/public/assets/img/credito.png" text="Credito" />
                                    <input type="radio" v-model="pick" :value="2" />
                                </v-col>
                                <v-col cols="3">
                                    <TypePayment img="/public/assets/img/debito.png" text="Debito" />
                                    <input type="radio" v-model="pick" :value="3" />
                                </v-col>
                                <v-col cols="3">
                                    <TypePayment img="/public/assets/img/transferir.png" text="Transferir" />
                                    <input type="radio" v-model="pick" :value="4" />
                                </v-col>
                            </v-row>
                            <v-divider class="my-3" />
                            <div v-if="pick === 1">
                                <label class="text-uppercase">Tipo de Persona</label>
                                <SelectCustom dark label="Tipo de persona" :items="langSelectItems" />
                                <label class="text-uppercase">Banco</label>
                                <SelectCustom dark label="Seleccionar banco" :items="langSelectItems" />
                            </div>
                            <div v-if="pick === 2">
                                <label class="text-uppercase">Numero de tarjeta</label>
                                <CustomInput />
                                <label class="text-uppercase">Nombre titular de la tarjeta</label>
                                <CustomInput />
                                <label class="text-uppercase">Fecha de expedicion</label>
                                <SelectCustom dark label="Tipo de persona" :items="langSelectItems" />
                                <label class="text-uppercase">Codigo de seguridad</label>
                                <CustomInput />
                                <label class="text-uppercase">Numero de CVV2</label>
                                <CustomInput />
                                <label class="text-uppercase">Documento</label>
                                <SelectCustom dark label="Seleccionar banco" :items="langSelectItems" />
                                <label class="text-uppercase">Numero de documento</label>
                                <CustomInput />
                            </div>
                            <div v-if="pick === 3">
                                <label class="text-uppercase">Numero de tarjeta</label>
                                <CustomInput />
                                <label class="text-uppercase">Nombre titular de la tarjeta</label>
                                <CustomInput />
                                <label class="text-uppercase">Fecha de expedicion</label>
                                <SelectCustom dark label="Tipo de persona" :items="langSelectItems" />
                                <label class="text-uppercase">Codigo de seguridad</label>
                                <CustomInput />
                                <label class="text-uppercase">Numero de CVV2</label>
                                <CustomInput />
                                <label class="text-uppercase">Documento</label>
                                <SelectCustom dark label="Seleccionar banco" :items="langSelectItems" />
                                <label class="text-uppercase">Numero de documento</label>
                                <CustomInput />
                            </div>
                            <div v-if="pick === 4" class="form">
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 text-uppercase bold">PERSONA JURÍDICA</span>
                                    <span class="body1"> idovela sas </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 text-uppercase bold">CORREO ELECTRÓNICO</span>
                                    <span class="body1"> contabilidad@idovela.com </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 text-uppercase bold">TELÉFONO</span>
                                    <span class="body1"> 57 125 1254 1254 </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 text-uppercase bold">NIT</span>
                                    <span class="body1"> 546456546546 </span>
                                </div>
                                <div class="d-flex justify-space-between mb-2">
                                    <span class="subtitle1 text-uppercase bold">CUENTA DE AHORROS</span>
                                    <span class="body1"> 768678678 </span>
                                </div>
                                <CustomButton
                                    block
                                    color="grey"
                                    text="Añadir comprobante de pago"
                                    @click="$refs.fileInput.click()"
                                />
                                <input
                                    style="display: none;"
                                    ref="fileInput"
                                    type="file"
                                    @change="fileSelected"
                                    enctype="multipart/form-data"
                                />
                            </div>
                            <CustomButton class="mt-2" text="Aplicar" color="grey" />
                        </div>
                    </v-col>
                    <v-col cols="12" md="6" order="2" order-md="2" order-sm="1">
                        <div class="form">
                            <h6 class="black--text bold">Codigo Promocional</h6>
                            <v-divider class="my-3" />
                            <label class="black--text text-uppercase">(Regalo / Referido)</label>
                            <CustomInput />
                        </div>
                    </v-col>
                    <v-col cols="12" order="3" order-md="3" order-sm="3" class="d-flex justify-space-between">
                        <div class="mb-2">
                            <CustomButton text="Volver" color="nero" @click="step = 2" />
                        </div>
                        <total :total="priceTotal" />
                        <div class="mb-2">
                            <CustomButton
                                text="Continuar"
                                color="nero"
                                @click="proceedCheckout()"
                                :loading="checkoutLoading"
                                :disabled="checkoutLoading"
                            />
                        </div>
                    </v-col>
                </v-row>
            </v-stepper-content>
            <v-stepper-content step="4">
                <v-row>
                    <v-col cols="12">
                        <div class="div-alert">
                            <div class="information">
                                <div>
                                    <span class="success"><i class="las la-check"></i></span>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold">Gracias por registrarse</h6>
                                    <p class="body-1">
                                        Enviaremos al e-mail de facturación un correo de verificación por la compra
                                    </p>
                                </div>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <Order
                            :order="dataCheckout?.order_code"
                            :day="fecha?.getDate()"
                            :month="new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(fecha)"
                            :year="fecha?.getFullYear()"
                            colorStatus="red"
                            descriptionStatus="Por aprobar pedido"
                            icon1="la-download"
                            icon2="la-print"
                        />
                    </v-col>
                </v-row>
                <v-divider class="mb-3" />
                <v-row>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Facturar a</h6>
                                    <v-divider class="my-3" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Correo electronico</span>
                                        <span class="body1">{{ currentUser.email || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Tipo de Persona</span>
                                        <span class="body1">{{ currentUser.personType || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Nombre</span>
                                        <span class="body1">{{ currentUser.name || "--" }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-3">
                                        <span class="subtitle1 text-uppercase bold">Documento</span>
                                        <span class="body1">
                                            {{ currentUser.documentType || "--" }}
                                            {{ currentUser.documentNumber || "--" }}
                                        </span>
                                    </div>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de servicio</h6>
                                    <v-divider class="my-3" />

                                    <div
                                        v-if="
                                            Object.entries(addressServicio).length !== 0 && useDefaultAddress1 == false
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1">{{ addressServicio?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressServicio?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressServicio?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressServicio?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressServicio?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class=""
                                        v-if="
                                            Object.entries(addressPrincipal).length !== 0 && useDefaultAddress1 == true
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{ addressPrincipal?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressPrincipal?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressPrincipal?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressPrincipal?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressPrincipal?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Nombre de quien lo va a recibir
                                            </span>
                                            <span class="body1">{{ addressPrincipal?.name }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>

                                    <CustomButton
                                        v-if="
                                            Object.entries(addressFacturacion).length === 0 &&
                                            useDefaultAddress1 == false
                                        "
                                        block
                                        color="grey"
                                        text="Añadir Dirección"
                                        @click="openAdress('service')"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de facturacion</h6>
                                    <v-divider class="my-3" />
                                    <div
                                        v-if="
                                            Object.entries(addressFacturacion).length !== 0 &&
                                            useDefaultAddress2 == false
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{ addressFacturacion?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressFacturacion?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressFacturacion?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressFacturacion?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressFacturacion?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class=""
                                        v-if="
                                            Object.entries(addressPrincipal).length !== 0 && useDefaultAddress2 == true
                                        "
                                    >
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{ addressPrincipal?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{ addressPrincipal?.address }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{ addressPrincipal?.postal_code }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{ addressPrincipal?.country }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{ addressPrincipal?.city }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Nombre de quien lo va a recibir
                                            </span>
                                            <span class="body1">{{ addressPrincipal?.name }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{ addressPrincipal?.phone }}</span>
                                        </div>
                                    </div>

                                    <CustomButton
                                        v-if="
                                            Object.entries(addressFacturacion).length === 0 &&
                                            useDefaultAddress2 == false
                                        "
                                        block
                                        color="grey"
                                        text="Añadir Dirección"
                                        @click="openAdress('billing')"
                                    />
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-row>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de envio</h6>
                                    <v-divider class="my-3" />
                                    <SelectCustom dark label="Usuario Principal" :items="langSelectItems" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                        <span class="body1">Dirección principal</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Dirección</span>
                                        <span class="body1 text-right">{{ addressPrincipal?.address }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                        <span class="body1">{{ addressPrincipal?.address }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                        <span class="body1">{{ addressPrincipal?.postal_code }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Departamento</span>
                                        <span class="body1">{{ addressPrincipal?.country }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Municipio</span>
                                        <span class="body1">{{ addressPrincipal?.city }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Barrio</span>
                                        <span class="body1"> -- </span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">
                                            Nombre de quien lo va a recibir
                                        </span>
                                        <span class="body1">{{ addressPrincipal?.name }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                        <span class="body1">{{ addressPrincipal?.phone }}</span>
                                    </div>
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
                <div class="my-5">
                    <h6>Lista de Pedido</h6>
                    <v-divider class="my-3" />
                    <v-row>
                        <v-col cols="12" v-for="(product, i) in cartItems" :key="i">
                            <ProductCart :productDetails="product" @changeQty="changeQty" @changeQtyMinus="changeQty" />
                        </v-col>
                    </v-row>
                    <v-divider class="my-3" />
                    <div class="div-total">
                        <div>
                            <p class="subtitle-2 text-uppercase font-weight-bold">Sub-total</p>
                            <p class="body-2">{{ format_price(priceTotal) }} COP</p>
                        </div>
                        <div>
                            <p class="subtitle-2 text-uppercase font-weight-bold">Iva</p>
                            <p class="body-2">000.000.000 COP</p>
                        </div>
                        <div>
                            <p class="subtitle-2 text-uppercase font-weight-bold">Flete</p>
                            <p class="body-2">000.000.000 COP</p>
                        </div>
                        <div>
                            <p class="subtitle-1 text-uppercase font-weight-bold">Total</p>
                            <p class="body-1">{{ format_price(priceTotal) }} COP</p>
                        </div>
                    </div>
                    <v-divider class="my-3" />
                </div>
                <v-row>
                    <v-col cols="12" sm="6">
                        <div class="form">
                            <h6 class="black--text bold">Seguimiento de envió</h6>
                            <v-divider class="my-3" />
                            <v-timeline dense>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold"> En verificación </span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">jueves 07, abril</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">Facturación</span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">sabado 09, abril</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">Alistamiento</span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">lunes 05, julio</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">
                                                Recogido por transportadora
                                            </span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">miércoles 07, julio</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">
                                                Entregado a cliente
                                            </span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">viernes 09, julio</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                            </v-timeline>
                            <a href="#" class="black--text text-decoration-underline font-weight-bold">
                                Conocer la logistica de envió
                            </a>
                        </div>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <div class="form">
                            <h6 class="black--text bold">Seguimiento de servicio</h6>
                            <v-divider class="my-3" />
                            <v-timeline dense>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">Comprado</span>
                                        </v-col>
                                        <v-col>
                                            <span class="body2 font-weight-bold">jueves 07, abril</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">Agendamiento</span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold"> En preparación </span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">
                                                Verificación de adecuación
                                            </span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                                <v-timeline-item color="black" small fill-dot>
                                    <v-row>
                                        <v-col>
                                            <span class="body2 text-uppercase font-weight-bold">
                                                Instalación concluida
                                            </span>
                                        </v-col>
                                    </v-row>
                                </v-timeline-item>
                            </v-timeline>
                            <a href="#" class="black--text text-decoration-underline font-weight-bold">
                                Conocer la logistica de envió
                            </a>
                        </div>
                    </v-col>
                    <v-col cols="12" class="d-flex justify-space-between">
                        <div class="mb-2">
                            <CustomButton text="Volver" color="nero" @click="step = 3" />
                        </div>
                        <total :total="cartPrice" />
                    </v-col>
                </v-row>
            </v-stepper-content>
        </v-stepper-items>
        </v-stepper>
    </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from "vuex";

import AddressDialog from "../../components/address/AddressDialog.vue";
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import Order from "../../components/global/Order.vue";
import ProductCart from "../../components/global/ProductCart.vue";
import ProfileDialog from "../../components/user/ProfileDialog.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import StepOrder from "../../components/icons/StepOrder.vue";
import Total from "../../components/global/Total.vue";
import TypePayment from "../../components/global/TypePayment.vue";

export default {
    components: {
        AddressDialog,
        CustomButton,
        CustomInput,
        Order,
        ProductCart,
        ProfileDialog,
        SelectCustom,
        StepOrder,
        Total,
        TypePayment
    },
    data() {
        return {
            langSelectItems: ["COLOMBIA", "DEUTSCHLAND", "ENGLAND"],
            step: 1,
            numberPag: 1,
            cartItems: [],
            priceTotal: 0,
            addDialogShow: false,
            addressSelectedForEdit: {},
            addressPrincipal: {},
            addressServicio: {},
            addressFacturacion: {},
            typeAddress: "shipping",
            useDefaultAddress1: false,
            useDefaultAddress2: false,
            profileSelectedForEdit: {},
            profileDialogShow: false,
            checkoutLoading: false,
            dataCheckout: {},
            fecha: new Date(),
            pick: 1
        };
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("cart", ["cartProducts", "cartPrice"])
    },
    mounted() {
        this.$vuetify.theme.dark = false;
    },
    methods: {
        ...mapActions("auth", ["getUser"]),
        async getCart() {
            const res = await this.call_api("post", `carts`, {});
            let _cartItems = [];

            if (res.data.success) {
                this.priceTotal = 0;

                _cartItems = res.data?.cart_items?.data;
                _cartItems.map(product => {
                    this.priceTotal += product?.regular_price * product?.qty;
                });

                let cartcollections = res.data?.cart_collections;
                cartcollections?.map(col => {
                    this.priceTotal += parseFloat(col?.collection?.precio) * col?.quantity;

                    _cartItems.push({
                        reference: col?.collection?.referencia,
                        name: col?.collection?.coleccion,
                        regular_price: parseFloat(col?.collection?.precio),
                        brandName: col?.brand?.name,
                        cart_id: col?.id,
                        qty: col?.quantity,
                        isCollection: true,
                        products: col?.products
                    });
                });

                this.cartItems = _cartItems;
            }
        },
        async getAddresses() {
            const res = await this.call_api("get", `user/addresses`);
            // this.snack({ message: `Please select a cart product`, color: "red" });
            if (res.data.success) {
                res?.data?.data?.map(address => {
                    if (address?.default_shipping == 1) {
                        this.addressPrincipal = address;
                    }
                    if (address?.default_billing == 1) {
                        this.addressFacturacion = address;
                    }
                    if (address?.default_service == 1) {
                        this.addressServicio = address;
                    }
                });
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
                this.$router.push({ name: "404" });
            }
        },
        changeQty(i) {
            this.getCart();
        },
        addressDialogClosed() {
            this.addressSelectedForEdit = {};
            this.addDialogShow = false;
            this.getAddresses();
        },
        editAddress(address, type) {
            this.typeAddress = type;
            this.addressSelectedForEdit = address;
            this.addDialogShow = true;
        },
        openAdress(type) {
            this.typeAddress = type;
            this.addDialogShow = true;
        },
        editProfile() {
            this.profileSelectedForEdit = this.currentUser;
            this.profileDialogShow = true;
        },
        profileDialogClosed() {
            this.profileSelectedForEdit = {};
            this.profileDialogShow = false;
            this.getUser();
        },
        async proceedCheckout() {
            if (Object.entries(this.dataCheckout).length === 0) {
                // prettier-ignore
                const shippingAddressId = this.useDefaultAddress1 ? this.addressPrincipal?.id ?? "" : this.addressServicio?.id ?? "";
                // prettier-ignore
                const billingAddressId = this.useDefaultAddress2 ? this.addressPrincipal?.id ?? "" : this.addressFacturacion?.id ?? "";

                let formData = new FormData();
                formData.append("shipping_address_id", shippingAddressId);
                formData.append("billing_address_id", billingAddressId);
                formData.append("delivery_type", "standard");

                this.cartItems.forEach((item, index) => {
                    if (item?.isCollection) {
                        formData.append("cart_collection_ids[]", item?.cart_id);
                    } else {
                        formData.append("cart_item_ids[]", item?.cart_id);
                    }
                });

                if (this.priceTotal > 0) {
                    this.checkoutLoading = true;
                    const res = await this.call_api("post", "checkout/order/store", formData);

                    if (res.data.success) {
                        this.dataCheckout = res.data;
                        this.step = 4;
                    } else {
                        this.snack({
                            message: res.data.message,
                            color: "red"
                        });
                    }

                    this.checkoutLoading = false;
                }
            } else {
                this.step = 4;
            }
        },
        fileSelected(evt) {
            evt.preventDefault();
            this.selectedFile = evt.target.files[0];
            this.uploadImage();
        },
        async uploadImage() {
            var formData = new FormData();
            formData.append("image", this.selectedFile, this.selectedFile.data);
            const config = {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            };
            const res = await this.call_api("post", "payment/image", formData, config);
        }
    },
    async created() {
        this.getCart();
        this.getAddresses();
        this.getUser();
    }
};
</script>

<style lang="scss" scoped>
::v-deep {
    // .v-stepper__step {
    //     padding: 2px;
    //     flex: 1;
    // }

    // .v-stepper__header {
    //     height: 40px;
    //     box-shadow: none;
    // }

    // .v-stepper__content {
    //     // padding: 12px 8px 16px;
    //     padding: 0;
    // }

    // .v-stepper__step__step {
    //     display: none;
    // }

    // .v-stepper__label {
    //     display: block !important;
    //     font-size: 12px;
    // }

    @media (min-width: 600px) {
        .v-stepper__label {
            font-size: 16px;
        }
    }

    .v-timeline {
        padding-top: 0px;
        left: 0px;
    }

    .v-timeline::before {
        background-color: #000000;
    }

    .v-application--is-ltr,
    .v-timeline--dense:not(.v-timeline--reverse)::before {
        left: 11px;
    }

    .v-timeline-item__divider {
        display: flex;
        justify-content: flex-start;
    }
}
.theme--light.v-stepper {
    background: #fafcfc;
}

.span-header {
    position: relative;
    top: 10px;
}

.v-stepper__header {
    height: 100px !important;
    border: 1px solid #e4e6e6 !important;
    border-radius: 10px !important;
    box-shadow: none !important;
}
::v-deep.cartSteper {
    &-card {
        border-radius: 10px;
    }

    .v-stepper__header {
        height: 95px !important;
        border: 1px solid #e4e6e6 !important;
        border-radius: 10px !important;

        .v-divider {
            border-color: rgba(#707070, 0.5);
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
                    &:not(.v-stepper__step--active) {
                        .v-stepper__step__step {
                            background: rgba(#000000, 0.25);
                        }
                    }

                    &.v-stepper__step--complete {
                        .v-stepper__step__step {
                            background: rgba(0, 0, 0, 0.87);
                        }
                    }
                }
            }
        }
    }

    &-step {
        display: block;
        text-align: center;
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
.cart {
    &-table {
        &-header {
            display: grid;
            align-items: stretch;
            grid-template-columns: 5fr 2fr 2fr 3fr;

            div {
                font-size: var(--font-size-body1);
                font-weight: 700;

                &:not(:first-child) {
                    padding-left: 1.5rem;
                }
            }
        }
    }
}

.car-items {
    gap: 0.5rem;
}

// Estilos carrito vacio //

.emptycart {
    display: flex;
    justify-content: center;
}
.cuadro-emptycart {
    padding: 40px 50px;
    text-align: center;
    border: 1px solid #dfdfdf;
    margin-top: 7%;
    border-radius: 10px;
}
.text-cartempty {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 0;
}
.img-cartempty {
    width: 26%;
    margin: auto;
}

// Fin estilos //
.container {
    background-color: #fafcfc;
}

.form {
    border: 1px solid #e4e4e4;
    border-radius: 10px;
    padding: 10px;
}

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

.div-total {
    display: flex;
    flex-direction: column;
}

.div-total div {
    display: flex;
    justify-content: flex-end;
}

.div-total p {
    padding-left: 5px;
}

.div-alert {
    background-color: #dfdfdf;
    border-radius: 10px;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.information {
    display: flex;
    align-items: center;
    gap: 10px;
}

@media (max-width: 600px) {
    .div-alert {
        display: flex;
        flex-wrap: wrap;
    }
}

.success {
    background-color: #06c167;
    color: #ffffff;
    border-radius: 50%;
    padding: 5px 5px 0px 5px;
}
</style>
