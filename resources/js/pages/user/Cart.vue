<template>
    <div>
        <v-stepper elevation="0" v-model="numberPag">
            <v-stepper-header v-if="cartProducts.length > 0">
                <v-stepper-step
                    :class="numberPag > 1 ? 'v-stepper__step--complete' : ''"
                    class="cartSteper-step"
                    step="1"
                    ><span class="span-header">PEDIDO</span></v-stepper-step
                >

                <v-divider :class="numberPag > 1 ? 'step-active' : ''" />

                <v-stepper-step
                    :class="numberPag > 2 ? 'v-stepper__step--complete' : ''"
                    class="cartSteper-step"
                    step="2"
                >
                    <span class="span-header">ENVIOS</span>
                </v-stepper-step>

                <v-divider :class="numberPag > 2 ? 'step-active' : ''" />

                <v-stepper-step
                    :class="numberPag > 3 ? 'v-stepper__step--complete' : ''"
                    class="cartSteper-step"
                    step="3"
                    ><span class="span-header">PAGO</span></v-stepper-step
                >

                <v-divider :class="numberPag > 3 ? 'step-active' : ''" />

                <v-stepper-step class="cartSteper-step" step="4"
                    ><span class="span-header">RESUMEN</span>
                </v-stepper-step>
            </v-stepper-header>

            <!-- <v-divider class="cartSteper-subheader" /> -->

            <v-stepper-items>
                <v-stepper-content class="tamaño-responsive" step="1">
                    <template v-if="cartProducts.length > 0">
                        <div class="cart-table-header mb-2" style="width: 100%">
                            <div style="padding-left: 4%">Productos</div>
                            <div>Precio</div>
                            <div class="d-none d-md-flex">Cantidad</div>
                            <div class="d-flex d-md-none">Cant</div>
                            <div class="d-none d-sm-flex">Opciones</div>
                            <div class="d-flex d-sm-none">Opc</div>
                        </div>
                        <v-row no-gutters class="car-items" style="overflow-y: auto; max-height: 450px">
                            <v-col cols="12" v-for="(product, i) in cartProducts" :key="i">
                                <ProductCart style="box-shadow: rgba(0, 0, 0, 0.16) 0px 4px 6px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px !important;" :productDetails="product" productCartType="checkout" />
                            </v-col>
                        </v-row>

                        <v-row class="barra-inferior">
                            <v-col
                                cols="12"
                                md="10"
                                class="d-flex justify-center justify-md-center align-center pl-0 pl-md-3 gap"
                            >
                                <total :total="priceTotal" />
                                <v-divider vertical></v-divider>
                                <div class="icono-cantidad">
                                    <CustomFavorite />
                                    <span style="color: #919191; margin-left: 10px">{{ getCartCount }}</span>
                                </div>
                                <v-divider vertical></v-divider>
                                <!-- <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
                                    DETALLES
                                    <i
                                        class="las la-sort-down"
                                        style="color: white; font-size: 20px; padding-left: 10px; padding-bottom: 5px"
                                    >
                                    </i>
                                </CustomButton>
                                <CustomButton color="grey2" @click="mostrarDetalle" v-else>
                                    DETALLES
                                    <i class="las la-sort-up" style="color: black; font-size: 20px; padding-left: 10px">
                                    </i>
                                </CustomButton> -->
                            </v-col>
                            <v-col cols="12" md="2" class="d-flex justify-end align-center pl-md-3">
                                <div class="boton-continuar pr-md-5" v-if="mostrarDetalles == false">
                                    <CustomButton
                                        class="boton-continuar"
                                        text="Continuar >"
                                        color="orange"
                                        @click="numberPag = 2"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12" style="justify-content: center" v-if="mostrarDetalles">
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Descuento detalle
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Recargo detalle
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3" style="font-size: 15px">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            TOTAL BRUTO FACTURA
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold" style="font-size: 15px"
                                            >000.000.000 COP</span
                                        >
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            IVA
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Otros impuestos
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start; font-size: 15px"
                                        >
                                            TOTAL IMPUESTO
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold" style="font-size: 15px"
                                            >000.000.000 COP</span
                                        >
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete ICA
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete IVA
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete fuente
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="0" md="2" class="py-0" />
                                    <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start; font-size: 15px"
                                        >
                                            TOTAL RETENCIONES
                                        </span>
                                    </v-col>
                                    <v-col cols="6" md="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold" style="font-size: 15px"
                                            >000.000.000 COP</span
                                        >
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col style="display: flex; justify-content: flex-end; align-items: center">
                                        <CustomButton text="Continuar >" color="grey2" @click="numberPag = 2" />
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </template>
                    <v-row v-else>
                        <v-col>
                            <div class="emptycart">
                                <div class="cuadro-emptycart">
                                    <v-img class="img-cartempty mb-6" src="/public/assets/img/icons/facturas.svg" />
                                    <p class="text-cartempty">AUN NO HAY PRODUCTOS EN LA LISTA DE PEDIDOS</p>
                                    <CustomButton
                                        text="IR A PRODUCTOS"
                                        color="orange"
                                        class="mt-2"
                                        :to="{ name: 'Shop' }"
                                    />
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-stepper-content>
                <v-stepper-content class="tamaño-responsive" step="2">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-row>
                                <v-col cols="12">
                                    <h5 class="fw-600 mb-5">Dirección de envío</h5>
                                    <div class="form mb-5">
                                        <v-row>
                                            <v-col cols="10" sm="11">
                                                <SelectCustom
                                                    dark="true"
                                                    label="Usuario Principal"
                                                    color="cart-select"
                                                    :items="addressesParaEnvio"
                                                    @input="changeAddress($event, 0)"
                                                    item-text="name"
                                                    item-value="id"
                                                    placeholder="Seleccione una opcion"
                                                />
                                            </v-col>
                                            <v-col cols="2" sm="1" class="pl-0 ojo">
                                                <i
                                                    class="las la-eye-slash"
                                                    style="font-size: 25px"
                                                    v-if="mostrarDatosEnvio"
                                                    @click="toggleDatosEnvio"
                                                ></i>
                                                <i
                                                    class="las la-eye"
                                                    style="font-size: 25px"
                                                    @click="toggleDatosEnvio"
                                                    v-else
                                                ></i>
                                            </v-col>
                                        </v-row>
                                        <v-divider class="my-3" />

                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Nombre de dirección</span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.name ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Dirección</span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.address ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3"> Dirección adicional </span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.details ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Código postal</span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.postal_code ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Departamento</span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.country ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Municipio</span>
                                            <span class="body1 pr-3">{{
                                                selectedAddressEnvio?.city ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <!-- <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Localidad</span>
                                            <span class="body1 pr-3"> -- </span>
                                        </div> -->
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Barrio</span>
                                            <span class="body1 pr-3">
                                                {{
                                                    selectedAddressEnvio?.neighborhood ||
                                                    "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Teléfono / Celular</span>
                                            <span class="body1 pr-3">{{
                                                addressPrincipal?.phone | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>

                                        <CustomButton
                                            v-if="Object.entries(addressPrincipal).length !== 0"
                                            class="mr-3 ml-3"
                                            style="width: 136px"
                                            color="white"
                                            text="EDITAR"
                                            @click="editAddress(addressPrincipal, 'shipping')"
                                        />
                                        <CustomButton
                                            v-if="Object.entries(addressPrincipal).length !== 0"
                                            class="mr-3 ml-3"
                                            color="white"
                                            text="AÑADIR"
                                            style="width: 136px"
                                            @click="openAdress('shipping')"
                                        />
                                        <CustomButton
                                            v-if="Object.entries(addressPrincipal).length === 0"
                                            block
                                            class="mr-3 ml-3"
                                            color="white"
                                            text="AÑADIR"
                                            @click="openAdress('shipping')"
                                        />
                                    </div>
                                    <div class="form mb-5" v-if="addDialogShow == true">
                                        <AddressDialog
                                            :typeAddress="typeAddress"
                                            :show="addDialogShow"
                                            @close="addressDialogClosed"
                                            :old-address="addressSelectedForEdit"
                                        />
                                    </div>
                                </v-col>
                                <v-col cols="12"> </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12" md="6">
                            <ProfileDialog
                                :show="profileDialogShow"
                                @close="profileDialogClosed"
                                :old-profile="profileSelectedForEdit"
                            />
                            <v-row>
                                <v-col cols="12">
                                    <h5 class="fw-600 mb-5">Facturar a nombre de</h5>
                                    <div class="form" style="gap: 32px">
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Nombre completo</span>
                                            <span class="body1 pr-3">{{
                                                capitalizeWords(currentUser.name) || "--"
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Correo electrónico</span>
                                            <span class="body1 pr-3">{{ currentUser.email || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Tipo de persona</span>
                                            <span class="body1 pr-3">{{ currentUser.personType || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Dirección</span>
                                            <span class="body1 pr-3">{{ selectedAddressEnvio?.address || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Tipo de documento</span>
                                            <span class="body1 pr-3">{{ currentUser.documentType || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Número de documento</span>
                                            <span class="body1 pr-3">{{ currentUser.documentNumber || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-3">
                                            <span class="subtitle1 bold pl-3">Teléfono / celular</span>
                                            <span class="body1 pr-3">
                                                {{ currentUser.phone || "--" }}
                                            </span>
                                        </div>
                                        <!-- <CustomButton 
                                            class="mr-3 ml-3"
                                            style="width: 136px"
                                            color="grey"
                                            text="EDITAR" 
                                            @click="editProfile()" 
                                        /> -->
                                    </div>
                                    <!-- <h5 class="fw-600">Costo logístico</h5>
                                    <v-divider class="my-4" />
                                    <div class="form">
                                        <v-row>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">Flete</h6>
                                            </v-col>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">Precio</h6>
                                            </v-col>
                                        </v-row>

                                        <v-divider class="my-3" />

                                        <v-row>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">ENVÍO DE MERCANCÍA</h6>
                                            </v-col>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">000.000.000 COP</h6>
                                            </v-col>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">MOVILIDAD DE SERVICIO</h6>
                                            </v-col>
                                            <v-col cols="6">
                                                <h6 class="black--text bold">000.000.000 COP</h6>
                                            </v-col>
                                        </v-row>

                                        <v-divider class="my-3" />

                                        <v-row>
                                            <v-col cols="5"></v-col>
                                            <v-col cols="7">
                                                <h6 class="black--text bold">COSTO 000.000.000 COP</h6>
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn
                                                    id="customButton"
                                                    block
                                                    class="mt-4 boton-guardar"
                                                    text="Guardar >"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <i
                                                        class="las la-exclamation-circle"
                                                        style="font-size: 30px; transform: rotate(180deg)"
                                                    ></i
                                                    >MÁS INFORMACIÓN
                                                </v-btn>
                                                <div class="tooltip" id="tooltip">
                                                    El costo logístico se suma al valor subtotal de la compra
                                                    <br />
                                                    al pagar. El precio del envío de mercancía es gratuito en
                                                    <br />
                                                    la ciudad capital al superar el valor de 500.000 COP en el
                                                    <br />
                                                    pedido. (Valor sujeto a cambio) El costo de movilidad de
                                                    <br />
                                                    servicio varía dependiendo la ubicación de la dirección de
                                                    <br />
                                                    servicio. Para saber más
                                                    <a
                                                        class="text-decoration-underline"
                                                        href="https://www.google.com/"
                                                        target="_blank"
                                                        style="color: white; font-size: 17px"
                                                        >Conocer logística de envío</a
                                                    >
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </div> -->

                                    <br />
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <v-row class="barra-inferior">
                                <v-col
                                    class="d-none d-md-flex justify-start justify-md-end align-center pl-0 pl-md-3"
                                    cols="2"
                                    style="display: flex"
                                >
                                    <div v-if="mostrarDetalles == false">
                                        <CustomButton text="< Volver" color="white" @click="numberPag = 1" />
                                    </div>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="8"
                                    class="d-flex justify-center align-center gap pl-0 pl-md-3"
                                >
                                    <total :total="priceTotal" />
                                    <v-divider vertical></v-divider>
                                    <div class="icono-exclamacion d-flex">
                                        <v-tooltip top color="black">
                                            <template v-slot:activator="{ on, attrs }">
                                                <i
                                                    class="las la-exclamation-circle"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    style="font-size: 30px; transform: rotate(180deg)"
                                                ></i>
                                            </template>
                                            <span>El costo logístico se suma al valor subtotal</span>
                                            <br />
                                            <span>de la compra al pagar. El precio del Envío de</span>
                                            <br />
                                            <span>mercancia es gratuito en la ciudad capital al</span>
                                            <br />
                                            <span>superar el valor de 500.000 COP en el pedido.</span>
                                            <br />
                                            <span>(Valor sujeto a cambio) El costo de movilidad</span>
                                            <br />
                                            <span>de servicio varía dependiendo la ubicación de</span>
                                            <br />
                                            <span>la dirección de servicio. Para saber más.</span>
                                        </v-tooltip>
                                    </div>
                                    <v-divider vertical></v-divider>
                                    <!-- <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
                                        DETALLES
                                        <i
                                            class="las la-sort-down"
                                            style="
                                                color: white;
                                                font-size: 20px;
                                                padding-left: 10px;
                                                padding-bottom: 5px;
                                            "
                                        >
                                        </i>
                                    </CustomButton>
                                    <CustomButton color="grey2" @click="mostrarDetalle" v-else>
                                        DETALLES
                                        <i
                                            class="las la-sort-up"
                                            style="color: black; font-size: 20px; padding-left: 10px"
                                        >
                                        </i>
                                    </CustomButton> -->
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="2"
                                    style="display: flex; justify-content: flex-end; align-items: center"
                                >
                                    <v-row>
                                        <v-col cols="6" md="0" class="d-flex d-md-none">
                                            <div v-if="mostrarDetalles == false" class="boton-continuar">
                                                <CustomButton
                                                    class="boton-continuar"
                                                    text="< Volver"
                                                    color="nero"
                                                    @click="numberPag = 1"
                                                />
                                            </div>
                                        </v-col>
                                        <v-col cols="6" md="12" class="pl-0">
                                            <div v-if="mostrarDetalles == false" class="boton-continuar">
                                                <CustomButton
                                                    class="boton-continuar"
                                                    text="Continuar >"
                                                    color="orange"
                                                    @click="goStepThree"
                                                />
                                            </div>
                                        </v-col>
                                    </v-row>
                                </v-col>
                                <v-col cols="12" style="justify-content: center" v-if="mostrarDetalles">
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Envío de mercancia
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Movilidad de servicio
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="0" md="2" class="py-0" />
                                        <v-col cols="6" md="3" class="py-0 pl-0 pl-md-3">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="6" md="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col
                                            class="boton-continuar pl-0 pl-md-3"
                                            style="display: flex; justify-content: flex-start; align-items: center"
                                        >
                                            <CustomButton
                                                class="boton-continuar"
                                                text="< Volver"
                                                color="nero"
                                                @click="numberPag = 1"
                                            />
                                        </v-col>
                                        <v-col
                                            class="boton-continuar pl-0 pl-md-3"
                                            style="display: flex; justify-content: flex-end; align-items: center"
                                        >
                                            <CustomButton
                                                class="boton-continuar"
                                                text="Continuar >"
                                                color="grey2"
                                                @click="goStepThree"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-stepper-content>
                <v-stepper-content class="tamaño-responsive" step="3">
                    <v-row>
                        <v-col cols="12" md="7" order="1" order-md="1" order-sm="1">
                            <h5 class="fw-600 pt-1">Seleccionar medio de pago</h5>
                            <div class="form px-0" style="border: none !important; box-shadow: none">
                                <v-row>
                                    <v-col cols="6" sm="3">
                                        <div
                                            :class="{ 'active-payment': pick == 1 }"
                                            @click="pick = 1"
                                            style="cursor: pointer"
                                        >
                                            <TypePayment
                                                :active="pick === 1"
                                                img="/public/assets/img/pse.svg"
                                                text="PSE"
                                            />
                                            <label class="custom-radio ml-3">
                                                <input
                                                    type="radio"
                                                    name="medio-de-pago"
                                                    autocomplete="off"
                                                    v-model="pick"
                                                    :value="1"
                                                />
                                                <span class="radio-check d-none"></span>
                                            </label>
                                        </div>
                                    </v-col>
                                    <v-col cols="6" sm="3">
                                        <div
                                            :class="{ 'active-payment': pick == 2 }"
                                            @click="pick = 2"
                                            style="cursor: pointer"
                                        >
                                            <TypePayment
                                                :active="pick === 2"
                                                img="/public/assets/img/Card.svg"
                                                text="Credito/Debito"
                                            />
                                            <label class="custom-radio ml-3">
                                                <input
                                                    type="radio"
                                                    name="medio-de-pago"
                                                    autocomplete="off"
                                                    v-model="pick"
                                                    :value="2"
                                                />
                                                <span class="radio-check d-none"></span>
                                            </label>
                                        </div>
                                    </v-col>
                                    <v-col cols="6" sm="3">
                                        <div
                                            :class="{ 'active-payment': pick == 5 }"
                                            @click="pick = 5"
                                            style="cursor: pointer"
                                        >
                                            <TypePayment
                                                :active="pick === 5"
                                                img="/public/assets/img/Contraentrega.svg"
                                                text="Contraentrega"
                                            />
                                            <label class="custom-radio ml-3">
                                                <input
                                                    type="radio"
                                                    name="medio-de-pago"
                                                    autocomplete="off"
                                                    v-model="pick"
                                                    :value="5"
                                                />
                                                <span class="radio-check d-none"></span>
                                            </label>
                                        </div>
                                    </v-col>
                                    <v-col cols="6" sm="3">
                                        <div
                                            :class="{ 'active-payment': pick == 4 }"
                                            @click="pick = 4"
                                            style="cursor: pointer"
                                        >
                                            <TypePayment
                                                :active="pick === 4"
                                                img="/public/assets/img/Transferencia.svg"
                                                text="Transferir"
                                            />
                                            <label class="custom-radio ml-3">
                                                <input
                                                    type="radio"
                                                    name="medio-de-pago"
                                                    autocomplete="off"
                                                    v-model="pick"
                                                    :value="4"
                                                />
                                                <span class="radio-check d-none"></span>
                                            </label>
                                        </div>
                                    </v-col>
                                </v-row>
                                <div class="mt-3">
                                    <div v-if="pick === 1" class="data-payments">
                                        <!-- <div class="mb-3">
                                            <strong>Para poder continuar con el pago, debes habilitar las ventanas emergentes</strong>
                                        </div>
                                        <CustomButton @click="dialogPSEModal = true" class="mb-4" block color="white" text="Tutorial" /> -->
                                        <div class="pt-4">
                                            <label>Tipo de Persona</label>
                                            <SelectCustom
                                                class="selector"
                                                :dark="darkBoxes"
                                                label="Tipo de persona"
                                                :items="selectPersonType"
                                                v-model="personTypeSelected"
                                                placeholder="Seleccionar una opción"
                                            />
                                        </div>
                                        <div class="pt-4">
                                            <label>Banco</label>
                                            <SelectCustom
                                                class="selector"
                                                :dark="darkBoxes"
                                                label="Seleccionar banco"
                                                placeholder="Seleccionar una opción"
                                                :items="selectBancos"
                                                v-model="bancoSelected"
                                            />
                                        </div>
                                        <div v-if="urlPagoPSE !== null" class="pt-4">
                                            <p v-if="isLinkVisible">{{urlPagoPSE}}</p>
                                            <CustomButton @click="openWindow(urlPagoPSE)" class="mb-4" block color="white" text="Click aqui para dirigirte al banco"/>
                                        </div>
                                    </div>
                                    <div v-if="pick === 2" class="data-payments">
                                        <div class="d-flex justify-content-between">
                                            <div class="mr-5">
                                                <input type="checkbox" class="form-check-input" :checked="isCredit" id="isCredit"  @change="toggleCheckbox('first')"/>
                                                <label class="form-check-label" for="isCredit">
                                                    Tarjeta Credito
                                                </label>
                                            </div>
                                            <div>
                                                <input type="checkbox" class="form-check-input" :checked="isDebit" id="isDebit"  @change="toggleCheckbox('second')"/>
                                                <label class="form-check-label" for="isDebit">
                                                    Tarjeta Debito
                                                </label>
                                            </div>
                                        </div>
                                        <div class="pt-4">
                                            <label>Numero de tarjeta</label>
                                            <CustomInput
                                                type="number"
                                                placeholder="Número tarjeta"
                                                v-model="formCard.number"
                                            />
                                        </div>
                                        <div class="pt-4">
                                            <label>Nombre del titular</label>
                                            <CustomInput placeholder="Nombre del titular" v-model="formCard.card_holder" />
                                        </div>
                                        <div class="pt-4">
                                            <label>Numero de CVC</label>
                                            <CustomInput placeholder="CVC" maxlength="3" v-model="formCard.cvc" />
                                        </div>
                                        <div class="pt-4">
                                            <label>Mes / año de expiración</label>
                                            <div class="d-flex justify-content-between">            
                                                <CustomInput class="col-md-3" placeholder="01" card="numberCard" maxlength="2" v-model="formCard.exp_month" />
                                                <CustomInput placeholder="29" card="numberCard" maxlength="2" v-model="formCard.exp_year" />
                                            </div>
                                        </div>
                                        <div class="pt-4" v-if="isCredit">
                                            <label>Numero de cuotas</label>
                                            <CustomInput
                                                placeholder="Numero de Cuotas"
                                                v-model="formCard.installments"
                                            />
                                        </div>
                                    </div>
                                    <div v-if="pick === 4">
                                        
                                        <p>
                                            Despachamos el producto una vez que se envíe la transferencia y el
                                            comprobante por WhatsApp
                                        </p>
                                        <v-img
                                            style="width: 500px; height: auto;"
                                            src="/public/assets/img/pago-contraentrega.svg"
                                            alt="Transferencia"
                                        />
                                        <div class="data-section">
                                            <h3>DATOS DE BANCO</h3>
                                            <div class="body-data">
                                                <p>Banco: Bancolombia</p>
                                                <p>Tipo de cuenta: Corriente</p>
                                                <p>Numero cuenta: 20500005130</p>
                                            </div>
                                        </div>

                                        <div class="data-section">
                                            <h3>DATOS DE PROPIETARIO DE CUENTA</h3>
                                            <div class="body-data">
                                                <p>Razón social: Aloranges S.A.S</p>
                                                <p>NIT: 901810257-1</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="pick === 5">
                                        <p>Solo pagas por el producto cuando te lo entregamos en tu domicilio.</p>
                                        <div class="d-flex justify-content-between">
                                            <div class="mr-5">
                                                <input type="checkbox" class="form-check-input" :checked="isEfectivo" id="isCredit"  @change="toggleCheckboxcontraentrega('first')"/>
                                                <label class="form-check-label" for="isCredit">
                                                    Pago con Efectivo
                                                </label>
                                            </div>
                                            <div>
                                                <input type="checkbox" class="form-check-input" :checked="isDatafono" id="isDebit"  @change="toggleCheckboxcontraentrega('second')"/>
                                                <label class="form-check-label" for="isDebit">
                                                    Pago con Datafono
                                                </label>
                                            </div>
                                        </div>
                                        <v-img
                                            style="width: 400px; height: auto"
                                            src="/public/assets/img/pago-transferencia.svg"
                                            alt="Contraentrega"
                                        />
                                    </div>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" order="3" order-md="3" order-sm="3" class="d-flex justify-space-between">
                            <v-row class="barra-inferior">
                                <v-col
                                    cols="2"
                                    class="d-none d-md-flex justify-center justify-md-center align-center"
                                >
                                    <div v-if="mostrarDetalles == false">
                                        <CustomButton text="< Volver" color="white" @click="numberPag = 2" />
                                    </div>
                                </v-col>
                                <v-col cols="12" md="8" class="gap-1 pl-md-3">
                                    <TotalPago :total="priceTotal" />

                                    <v-divider vertical></v-divider>
                                    <!-- <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
                                        DETALLES
                                        <i
                                            class="las la-sort-down"
                                            style="
                                                color: white;
                                                font-size: 20px;
                                                padding-left: 10px;
                                                padding-bottom: 5px;
                                            "
                                        >
                                        </i>
                                    </CustomButton>
                                    <CustomButton color="grey2" @click="mostrarDetalle" v-else>
                                        DETALLES
                                        <i
                                            class="las la-sort-up"
                                            style="color: black; font-size: 20px; padding-left: 10px"
                                        >
                                        </i>
                                    </CustomButton> -->
                                </v-col>

                                <v-col cols="6" class="d-flex d-md-none">
                                    <div v-if="mostrarDetalles == false" class="boton-continuar">
                                        <CustomButton
                                            class="boton-continuar"
                                            text="< Volver"
                                            color="nero"
                                            @click="numberPag = 2"
                                        />
                                    </div>
                                </v-col>

                                <v-col
                                    cols="6"
                                    md="2"
                                    style="display: flex; justify-content: flex-end; align-items: center"
                                >
                                    <div v-if="mostrarDetalles == false" class="boton-continuar">
                                        <CustomButton
                                            class="boton-continuar"
                                            text="Continuar"
                                            color="orange"
                                            @click.stop="proceedCheckout()"
                                            :loading="checkoutLoading"
                                            :disabled="checkoutLoading"
                                        />
                                    </div>
                                </v-col>
                                <v-col cols="12" v-if="mostrarDetalles">
                                    <div style="justify-content: center; max-height: 300px; overflow-y: scroll">
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col
                                                cols="12"
                                                md="4"
                                                style="display: flex; justify-content: flex-start; gap: 10px"
                                            >
                                                <total :total="priceTotal" />
                                                <v-divider vertical></v-divider>
                                                <div class="icono-cantidad">
                                                    <CustomFavorite />
                                                    <span style="color: #919191; margin-left: 10px">{{
                                                        getCartCount
                                                    }}</span>
                                                </div>
                                            </v-col>
                                            <v-col cols="0" md="3" />
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Descuento detalle
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Recargo detalle
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start; font-size: 15px"
                                                >
                                                    TOTAL BRUTO FACTURA
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold" style="font-size: 15px"
                                                    >000.000.000 COP</span
                                                >
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    IVA
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Otros impuestos
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start; font-size: 15px"
                                                >
                                                    TOTAL IMPUESTO
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold" style="font-size: 15px"
                                                    >000.000.000 COP</span
                                                >
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Rete ICA
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Rete IVA
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Rete fuente
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start; font-size: 15px"
                                                >
                                                    TOTAL RETENCIONES
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold" style="font-size: 15px"
                                                    >000.000.000 COP</span
                                                >
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col
                                                cols="12"
                                                md="6"
                                                style="display: flex; justify-content: flex-start; gap: 10px"
                                            >
                                                <TotalFlete :total="priceTotal" />
                                                <v-divider vertical></v-divider>
                                                <div class="icono-exclamacion d-flex">
                                                    <v-tooltip top color="black">
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <i
                                                                class="las la-exclamation-circle"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                                style="font-size: 30px; transform: rotate(180deg)"
                                                            ></i>
                                                        </template>
                                                        <span>El costo logístico se suma al valor subtotal</span>
                                                        <br />
                                                        <span>de la compra al pagar. El precio del Envío de</span>
                                                        <br />
                                                        <span>mercancia es gratuito en la ciudad capital al</span>
                                                        <br />
                                                        <span>superar el valor de 500.000 COP en el pedido.</span>
                                                        <br />
                                                        <span>(Valor sujeto a cambio) El costo de movilidad</span>
                                                        <br />
                                                        <span>de servicio varía dependiendo la ubicación de</span>
                                                        <br />
                                                        <span>la dirección de servicio. Para saber más.</span>
                                                    </v-tooltip>
                                                </div>
                                            </v-col>
                                            <v-col cols="0" md="1" />
                                        </v-row>

                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>

                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Envío de mercancia
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Movilidad de servicio
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Rete fuente
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    IVA
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start"
                                                >
                                                    Recargo detalle
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                        <v-row style="justify-content: center">
                                            <v-divider style="max-width: 60%; margin-right: 82px" />
                                        </v-row>
                                        <v-row>
                                            <v-col cols="0" md="2" class="py-0" />
                                            <v-col cols="6" md="3" class="py-0">
                                                <span
                                                    class="body2 font-weight-bold py-0"
                                                    style="display: flex; justify-content: flex-start; font-size: 15px"
                                                >
                                                    Otros impuestos
                                                </span>
                                            </v-col>
                                            <v-col cols="6" md="4" class="seguimiento py-0">
                                                <span class="body2 font-weight-bold">000.000.000 COP</span>
                                            </v-col>
                                        </v-row>
                                    </div>
                                    <v-row>
                                        <v-col style="display: flex; justify-content: flex-start; align-items: center">
                                            <CustomButton text="< Volver" color="nero" @click="numberPag = 2" />
                                        </v-col>
                                        <v-col style="display: flex; justify-content: flex-end; align-items: center">
                                            <CustomButton
                                                text="Continuar"
                                                color="nero"
                                                @click="proceedCheckout()"
                                                :loading="checkoutLoading"
                                                :disabled="checkoutLoading"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-stepper-content>
                <v-stepper-content class="tamaño-responsive" step="4">
                    <v-row>
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
                    <v-row>
                        <v-col cols="12" md="6">
                            <!-- <div class="form">
                                <h6 class="black--text bold">Seguimiento de envió</h6>
                                <v-divider class="my-3" />
                                <v-timeline dense>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">
                                                    En verificación
                                                </span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">jueves 07, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">Facturación</span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">sabado 09, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">Alistamiento</span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">lunes 05, julio</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col cols="8">
                                                <span class="body2 text-uppercase font-weight-bold">
                                                    Recogido por transportadora
                                                </span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">martes 07, julio</span>
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
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">viernes 09, julio</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                </v-timeline>
                                <a href="#" class="text-decoration-underline mt-3" style="color: #1fa0e9">
                                    Conocer la logística de envio
                                </a>
                            </div> -->
                        </v-col>
                    </v-row>
                    <div class="my-5">
                        <h5 class="fw-600">Lista de Pedido</h5>

                        <div class="lista-pedido">
                            <div class="cart-table-header" style="width: 100%">
                                <div style="padding-left: 4%">Productos</div>
                                <div>Precio</div>
                                <div class="d-none d-md-flex">Cantidad</div>
                                <div class="d-flex d-md-none">Cant</div>
                                <div class="d-none d-sm-flex">Opciones</div>
                                <div class="d-flex d-sm-none">Opc</div>
                            </div>
                            <v-row style="max-height: 450px; overflow-y: auto">
                                <!-- <v-col cols="12" v-for="(product, i) in cartItems" :key="i"> -->
                                <v-col cols="12" v-for="(product, i) in cartItemsTwo" :key="i"> 
                                    
                                    <ProductCart
                                        :productDetails="product"
                                        productCartType="bill"
                                        @changeQty="changeQty"
                                        @changeQtyMinus="changeQty"
                                    />
                                </v-col>
                            </v-row>
                        </div>
                        <div class="mt-4" style="background-color: #f1f1f1">
                            <v-row style="padding: 10px">
                                <v-col cols="8" sm="10" class="detalles-final-total pl-5">
                                    <TotalPago :total="priceTotal" />
                                </v-col>
                                <v-col cols="4" sm="2" class="detalles-final-details">
                                    <CustomButton
                                        color="black"
                                        v-if="mostrarDetallesFinal"
                                        @click="ocultarDetalleFinal"
                                    >
                                        DETALLES
                                        <i
                                            class="las la-sort-down"
                                            style="
                                                color: white;
                                                font-size: 20px;
                                                padding-left: 10px;
                                                padding-bottom: 5px;
                                            "
                                        >
                                        </i>
                                    </CustomButton>
                                    <CustomButton color="white" @click="mostrarDetalleFinal" v-else>
                                        DETALLES
                                        <i
                                            class="las la-sort-up"
                                            style="color: black; font-size: 20px; padding-left: 10px"
                                        >
                                        </i>
                                    </CustomButton>
                                </v-col>
                                <v-col cols="0" sm="6" class="py-0 px-0" />

                                <v-col cols="12" sm="6" v-if="mostrarDetallesFinal">
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" style="display: flex; justify-content: flex-start; gap: 5px">
                                            <total :total="priceTotal" />
                                            <v-divider vertical></v-divider>
                                            <div class="icono-cantidad">
                                                <CustomFavorite />
                                                <span style="color: #919191; margin-left: 10px">{{
                                                    getCartCount
                                                }}</span>
                                            </div>
                                        </v-col>
                                    </v-row>

                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>

                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Descuento detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL BRUTO FACTURA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL IMPUESTO
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete ICA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL RETENCIONES
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" style="display: flex; justify-content: flex-start; gap: 10px">
                                            <TotalFlete :total="priceTotal" />
                                            <v-divider vertical></v-divider>
                                            <div class="icono-exclamacion d-flex">
                                                <v-tooltip top color="black">
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <i
                                                            class="las la-exclamation-circle"
                                                            v-bind="attrs"
                                                            v-on="on"
                                                            style="font-size: 30px; transform: rotate(180deg)"
                                                        ></i>
                                                    </template>
                                                    <span>El costo logístico se suma al valor subtotal</span>
                                                    <br />
                                                    <span>de la compra al pagar. El precio del Envío de</span>
                                                    <br />
                                                    <span>mercancia es gratuito en la ciudad capital al</span>
                                                    <br />
                                                    <span>superar el valor de 500.000 COP en el pedido.</span>
                                                    <br />
                                                    <span>(Valor sujeto a cambio) El costo de movilidad</span>
                                                    <br />
                                                    <span>de servicio varía dependiendo la ubicación de</span>
                                                    <br />
                                                    <span>la dirección de servicio. Para saber más.</span>
                                                </v-tooltip>
                                            </div>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Envío de mercancia
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Movilidad de servicio
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6" class="espaciado">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="6" class="seguimiento espaciado">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </div>
                    </div>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-row>
                                <v-col cols="12">
                                    <div class="form" style="background-color: #f1f1f1">
                                        <v-row class="mb-2">
                                            <v-col cols="12" class="pb-0">
                                                <h5 class="fw-600 pl-3" style="font-size: 25px">Dirección de envío</h5>
                                            </v-col>
                                        </v-row>
                                        <div
                                            v-if="
                                                Object.entries(selectedAddressEnvio).length !== 0 &&
                                                useDefaultAddress2 == false
                                            "
                                        >
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Nombre de dirección</span>
                                                <span class="body1 pr-3">Dirección principal</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Dirección</span>
                                                <span class="body1 text-right pr-3">{{
                                                    selectedAddressEnvio?.address
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3"> Descripción de dirección </span>
                                                <span class="body1 pr-3">{{
                                                    selectedAddressEnvio?.address
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Codigo postal</span>
                                                <span class="body1 pr-3">{{
                                                    selectedAddressEnvio?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Departamento</span>
                                                <span class="body1 pr-3">{{
                                                    selectedAddressEnvio?.country
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Municipio</span>
                                                <span class="body1 pr-3">{{
                                                    selectedAddressEnvio?.city | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Barrio</span>
                                                <span class="body1 pr-3"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Telefono / Movil</span>
                                                <span class="body1 pr-3">{{
                                                    selectedAddressEnvio?.phone | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                        </div>

                                        <div
                                            class=""
                                            v-if="
                                                Object.entries(addressPrincipal).length !== 0 &&
                                                useDefaultAddress2 == true
                                            "
                                        >
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold pl-3"
                                                    >Nombre de Dirección</span
                                                >
                                                <span class="body1 pr-3">Dirección principal</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Dirección</span>
                                                <span class="body1 text-right pr-3">{{
                                                    addressPrincipal?.address | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3"> Descripción de dirección </span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.address | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Codigo postal</span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Departamento</span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.country | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Municipio</span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.city | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Barrio</span>
                                                <span class="body1 pr-3"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">
                                                    Nombre de quien lo va a recibir
                                                </span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.name | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 bold pl-3">Telefono / Movil</span>
                                                <span class="body1 pr-3">{{
                                                    addressPrincipal?.phone | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
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
                                <v-col cols="12">
                                    <div class="form" style="background-color: #f1f1f1">
                                        <h5 class="black--text pl-3 mb-2" style="font-size: 25px">Encargado</h5>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Correo electrónico</span>
                                            <span class="body1 pr-3">{{ currentUser.email || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Tipo de persona</span>
                                            <span class="body1 pr-3">{{ currentUser.personType || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Nombre</span>
                                            <span class="body1 pr-3">{{ currentUser.name || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-3">
                                            <span class="subtitle1 bold pl-3">Documento</span>
                                            <span class="body1 pr-3">
                                                {{ currentUser.documentType || "--" }}
                                                {{ currentUser.documentNumber || "--" }}
                                            </span>
                                        </div>
                                    </div>
                                </v-col>
                                
                            </v-row>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-row>
                                <v-col cols="12">
                                    <div class="form" style="background-color: #f1f1f1">
                                        <v-row class="mb-2">
                                            <v-col cols="12" class="pb-0">
                                                <h5 class="fw-600 pl-3" style="font-size: 25px">Facturar a nombre de</h5>
                                            </v-col>
                                        </v-row>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Nombre</span>
                                            <span class="body1 text-right pr-3">{{
                                                userData?.firstName | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Apellido</span>
                                            <span class="body1 text-right pr-3">{{
                                                userData?.firstLastname | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3">Documento</span>
                                            <span class="body1 text-right pr-3">{{
                                                userData?.documentNumber
                                                    | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                    </div>
                                </v-col>

                                <v-col cols="12">
                                    <div class="form mt-4" style="background-color: #f1f1f1">
                                        <h5 class="fw-600 pl-3 mb-2" style="font-size: 25px">Medio de pago</h5>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span v-if="pick == 4" class="subtitle1 bold pl-3">Transferencia bancaria</span>
                                            <span v-else-if="pick == 5" class="subtitle1 bold pl-3">Contraentrega</span>
                                            <span v-else-if="pick == 2" class="subtitle1 bold pl-3">
                                                <p v-if="isCredit">Tarjeta Credito</p>
                                                <p v-else-if="isDebit">Tarjeta Debito</p>
                                            </span>
                                            <span v-else-if="pick == 1" class="subtitle1 bold pl-3">PSE</span>
                                        </div>
                                    </div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="form" style="background-color: #f1f1f1">
                                        <h5 class="black--text pl-3 my-2" style="font-size: 25px">Código promocional</h5>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 bold pl-3"> Código (Regalo/Referido) </span>
                                            <span class="body1 pr-3">ALO4577</span>
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>

                    <v-divider class="my-4" />

                    <v-row>
                        <p style="font-size: 0.7rem; color: #b4b5b5; padding-left: 1.2rem; text-align: left">
                            Lorem ipsum odor amet, consectetuer adipiscing elit. Urna curabitur aliquet condimentum
                            facilisis ex nec morbi porta laoreet. Tempus fusce et mollis dolor est augue cursus. Fusce
                            et nisi venenatis ad neque tincidunt dapibus. Condimentum maximus in leo mollis nascetur
                            dignissim. Curae leo odio morbi efficitur sociosqu metus. Magnis feugiat hac conubia non
                            torquent commodo sit. Euismod metus hac ullamcorper tristique feugiat ut. Praesent turpis
                            magnis himenaeos quis adipiscing. Fusce sodales morbi dictum felis feugiat. Pellentesque
                            condimentum nisl integer tincidunt ac urna nascetur ante fermentum. Montes nec integer
                            sagittis eu accumsan orci lacus orci sociosqu. Cras etiam augue non fermentum finibus sed.
                            Cursus primis ridiculus in faucibus velit ultricies lectus. Amet tempus quam sollicitudin
                            aenean imperdiet accumsan imperdiet. Enim himenaeos rhoncus montes sollicitudin lacinia dis
                            mus commodo. Etiam vitae senectus phasellus hendrerit pretium mi adipiscing. Dui libero
                            montes; ante elementum augue lectus. Hac phasellus felis feugiat nulla ante. Interdum
                            malesuada sem finibus, etiam penatibus gravida molestie ante faucibus? Ipsum in metus nibh
                            faucibus dapibus proin suspendisse eget.
                        </p>
                    </v-row>
                </v-stepper-content>
            </v-stepper-items>
        </v-stepper>
        <v-dialog v-model="dialogPSEModal" max-width="600">
            <v-card>
                <v-card-title class="headline">Activación de Ventanas emergentes</v-card-title>
                <v-card-text>
                    <v-table theme="dark">
                        <thead>
                            <tr>
                                <th class="text-left">#</th>
                                <th class="text-left">Paso en Google</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>En tu ordenador, abre Chrome.</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Arriba a la derecha, haz clic en Más y luego Configuración.</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Haz clic en Privacidad y seguridad y luego Configuración de sitios y luego Ventanas emergentes y redirecciones.</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Elige la opción que quieras definir como predeterminada o elige la opción de añadir en la sección "Puede enviar ventanas emergentes y usar redirecciones" y agrega la url https://webapp.aloranges.com/</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closePSEModal">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
import TotalFlete from "../../components/global/TotalFlete.vue";
import TypePayment from "../../components/global/TypePayment.vue";
import Regalo from "../../components/icons/Regalo.vue";
import Promocion from "../../components/icons/Promocion.vue";
import Cubo from "../../components/icons/Cubo.vue";
import CustomFavorite from "../../components/icons/CustomFavorite.vue";
import Flecha from "../../components/icons/Flecha.vue";
import TotalPago from "../../components/global/TotalPago.vue";
import { forEach } from "lodash";

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
        TotalFlete,
        TotalPago,
        Regalo,
        Promocion,
        Cubo,
        TypePayment,
        CustomFavorite,
        Flecha
    },
    data() {
        return {
            langSelectItems: ["COLOMBIA", "DEUTSCHLAND", "ENGLAND"],
            step: 1,
            numberPag: 1,
            darkBoxes: true,
            cartItems: [],
            cartItemsTwo: [],
            priceTotal: 0,
            addDialogShow: false,
            addressSelectedForEdit: {},
            addressPrincipal: {},
            addressServicio: {},
            addressFacturacion: {},
            addressesParaFacturacion: [],
            addressesParaServicio: [],
            addressesParaEnvio: [],
            selectedAddressFacturacion: {},
            selectedAddressServicio: {},
            selectedAddressEnvio: {},
            typeAddress: "shipping",
            useDefaultAddress1: true,
            // useDefaultAddress1: false,
            useDefaultAddress2: false,
            profileSelectedForEdit: {},
            profileDialogShow: false,
            checkoutLoading: false,
            dataCheckout: {},
            fecha: new Date(),
            pick: 1,
            mostrarDatosEnvio: false,
            mostrarDatosServicio: false,
            mostrarDatosFacturacion: false,
            mostrarDetalles: false,
            mostrarDetallesFinal: false,
            userData: {},
            selectBancos: [],
            selectDocuments: ["Cedula Ciudadania", "Paraporte"],
            selectPersonType: [
                { text: "Natural", value: 0 },
                { text: "Juridico", value: 1 }
            ],
            personTypeSelected:"",
            bancoSelected:"",
            formCard:{},
            dialogPSEModal: false,
            urlPagoPSE:null,
            isCredit:true,
            isDebit:false,
            dialogTutorial: false,
            dialogPSEPaymentModal:false,
            referenceToPayment:null,
            isEfectivo:false,
            isDatafono:false,
            isLinkVisible:false,
        };
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("cart", ["cartProducts", "cartPrice"]),
        ...mapGetters("cart", ["getCartPrice", "getCartCount"])
    },
    mounted() {
        this.$vuetify.theme.dark = false;
        if (this.currentUser) {
            this.updateBreadcrumb();
        }
        this.wompiBanks();
    },
    filters: {
        filtroParaOcultarInfo(value, ocultarInfo) {
            if (ocultarInfo) {
                let conteoRestantes = 2;
                let caracteresIniciales = value.substring(0, 2);
                let primerosCaracteres = value.length;
                if (primerosCaracteres == 1) {
                    conteoRestantes = 1;
                    caracteresIniciales = value.substring(0, 1);
                }
                let caracteresRestantes = value.substring(conteoRestantes).replace(/./g, "*");
                let nuevaCadena = caracteresIniciales + caracteresRestantes;
                return nuevaCadena;
            }
            return value;
        }
    },
    watch: {
        currentUser(newValue) {
            if (newValue) {
                this.updateBreadcrumb();
            }
        },
        numberPag(newVal){
            if(newVal == 2){
                this.cartItemsTwo = [...this.$store.state.cart.cartProducts];
            }
        }
    },
    methods: {
        ...mapActions("auth", ["getUser"]),
        toggleCheckbox(option){
            if (option === 'first') {
                this.isCredit = true;
                this.isDebit = false;
            }else if(option === 'second'){
                this.isCredit = false;
                this.isDebit = true;
            }
        },
        toggleCheckboxcontraentrega(option){
            if (option === 'first') {
                this.isEfectivo=true;
                this.isDatafono=false;
            }else if(option === 'second'){
                this.isEfectivo=false;
                this.isDatafono=true;
            }
        },
        openWindow(url){
            window.open(url, '_blank', 'noopener,noreferrer');
            this.numberPag = 4;  
        },
        updateBreadcrumb() {
            const formattedName = this.capitalizeWords(this.currentUser.name);
            const newItems = [
                { text: "Home", href: "/", disabled: false },
                { text: formattedName, disabled: true }
            ];
            this.$store.dispatch("breadcrumb/setBreadcrumbItems", newItems);
        },
        goStepThree(){
            if(this.selectedAddressEnvio.name){
                this.numberPag = 3;
            }else{
                this.snack({
                        message: "Seleccione una dirección de envio",
                        color: "red"
                    });
            }
            
        },
        capitalizeWords(name) {
            return name.replace(/\b\w/g, char => char.toUpperCase());
        },
        toggleDatosEnvio() {
            this.mostrarDatosEnvio = !this.mostrarDatosEnvio;
        },
        toggleDatosServicio() {
            this.mostrarDatosServicio = !this.mostrarDatosServicio;
        },
        toggleDatosFacturacion() {
            this.mostrarDatosFacturacion = !this.mostrarDatosFacturacion;
        },

        mostrarDetalle() {
            this.mostrarDetalles = true;
        },
        ocultarDetalle() {
            this.mostrarDetalles = false;
        },
        mostrarDetalleFinal() {
            this.mostrarDetallesFinal = true;
        },
        ocultarDetalleFinal() {
            this.mostrarDetallesFinal = false;
        },

        filtradoParaInfo(cadena) {},
        changeAddress(event, direccionCambiar) {
            if (direccionCambiar === 0) {
                // Direccion de envio
                this.selectedAddressEnvio = this.addressesParaEnvio.find(x => x.id === event);
            }

            if (direccionCambiar === 1) {
                // Direccion de servicio
                this.selectedAddressServicio = this.addressesParaServicio.find(x => x.id === event);
            }

            if (direccionCambiar === 2) {
                // Direccion de facturacion
                this.selectedAddresFacturacion = this.addressesParaFacturacion.find(x => x.id === event);
            }
        },
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
                        this.addressesParaEnvio.push(address);
                    }
                    if (address?.default_billing == 1) {
                        this.addressesParaFacturacion.push(address);
                    }
                    if (address?.default_service == 1) {
                        this.addressesParaServicio.push(address);
                    } else {
                        this.addressesParaEnvio.push(address);
                        this.addressesParaFacturacion.push(address);
                        this.addressesParaServicio.push(address);
                    }
                });
                this.addressPrincipal = this.addressesParaEnvio;
                this.addressFacturacion = this.addressesParaFacturacion;
                this.addressServicio = this.addressesParaServicio;
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });
                this.$router.push({ name: "404" });
            }
        },
        async getInfoUser() {
            const res = await this.call_api("get", "user/info");
            if (res.data.success) {
                this.userData = res.data.user;
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
        async wompiBanks(){
            let result = await this.call_api('GET','product/payment-wompi-banks');
            result.data.banks.data.forEach(element => {
                this.selectBancos.push(
                    { text: element.financial_institution_name, value: element.financial_institution_code }
                )
            });
        },
        async verifyStatusPayment(dataToTransaction){
            let resultApi = await this.call_api('POST','product/transaction-wompi',dataToTransaction);
            if(resultApi.data.TransactionResult.data.payment_method.extra.async_payment_url){
                return resultApi.data.TransactionResult.data.payment_method.extra.async_payment_url;
                //window.location.href = this.urlPagoPSE;
            }else{
                this.verifyStatusPayment(dataToTransaction);
            }
        },

        closePSEModal(){
            this.dialogPSEModal = false;
        },
        processToSendStore(referenceToPayment){
            const shippingAddressId = this.selectedAddressEnvio.id;
            const billingAddressId = this.userData.id;
            let metodoPagoContraentregra = this.isEfectivo ? 'Efectivo' : 'Datafono'
            let formData = new FormData();
            formData.append("shipping_address_id", shippingAddressId);
            formData.append("billing_address_id", billingAddressId);
            formData.append("delivery_type", "standard");
            formData.append("code", referenceToPayment);
            formData.append("metodo_pago_contraentrega", metodoPagoContraentregra);

            this.cartItems.forEach((item, index) => {
                if (item?.isCollection) {
                    formData.append("cart_collection_ids[]", item?.cart_id);
                } else {
                    formData.append("cart_item_ids[]", item?.cart_id);
                }
            });
            return formData;
        },
        async verifyPaymentPSEStatus(){
            try{
                let result = await this.call_api('POST',`checkout/order/resultPse/${this.referenceToPayment}`);
                return result;
            }catch(error){
                this.snack({
                    message: 'Algo ha salido mal, Revisa la factura',
                    color: "red"
                });
                console.log(error);
                return null;
            }
        },
        async proceedCheckout() {
            if (Object.entries(this.dataCheckout).length === 0) {
                const date = new Date();
                const formattedDate = date.toISOString().slice(0, 10).replace(/-/g, "");
                const formattedTime = date.toTimeString().slice(0, 8).replace(/:/g, "");
                const randomNum = Math.floor(10 + Math.random() * 90);
                let referenceToPayment = formattedDate + "" + formattedTime + "" + randomNum;
                this.referenceToPayment = referenceToPayment;
                let result;
                if (this.priceTotal > 0) {
                    this.checkoutLoading = true;
                    if(this.pick === 2){
                        let totalPrice = this.priceTotal.toString();
                        let mountToPass = parseInt( totalPrice.replace(/[^\w\s]/gi, '') );
                        let total = parseInt(`${mountToPass}00`);
                        let data = {
                            mount: total,
                            currency: 'COP',
                            reference: referenceToPayment,
                            customer_email: this.userData.email,
                            customer_data: {
                                phone_number: this.userData.phone ? this.userData.phone : this.addressPrincipal.phone ? this.addressPrincipal.phone : '+573007819686',
                                full_name: this.userData.name,
                                legal_id: this.userData.documentNumber,
                                legal_id_type: this.userData.documentType.replace(/[^\w\s]/gi, ''),
                            },
                            shipping_address:{
                                address_line_1: this.selectedAddressEnvio.address,
                                country: "CO",
                                region: this.selectedAddressEnvio.state,
                                city: this.selectedAddressEnvio.city,
                                name: this.userData.name,
                                phone_number: this.userData.phone ? this.userData.phone : this.addressPrincipal.phone ? this.addressPrincipal.phone : '+573007819686',
                                postal_code: this.selectedAddressEnvio.postal_code
                            },
                            cardData: this.formCard,
                        };
                        result = await this.call_api('POST','product/payment-card-wompi',data);
                        if(result.data.success){
                            let formData = this.processToSendStore(this.referenceToPayment);
                            const res = await this.call_api("post", "checkout/order/store", formData);
                            this.numberPag = 4;
                            this.dataCheckout = res.data;
                        }else{
                            this.snack({
                                message: 'Algo ha salido mal, Revisa la información e intenta nuevamente',
                                color: "red"
                            });
                        }
                    }else if (this.pick === 1){
                        let totalPrice = this.priceTotal.toString();
                        let mountToPass = parseInt( totalPrice.replace(/[^\w\s]/gi, '') );
                        let total = parseInt(`${mountToPass}00`);
                        let data = {
                            mount: total,
                            currency: 'COP',
                            reference: referenceToPayment,
                            customer_email: this.userData.email,
                            customer_data: {
                                phone_number: this.userData.phone ? this.userData.phone : this.addressPrincipal.phone ? this.addressPrincipal.phone : '+573007819686',
                                full_name: this.userData.name,
                                legal_id: this.userData.documentNumber,
                                legal_id_type: this.userData.documentType.replace(/[^\w\s]/gi, ''),
                            },
                            shipping_address:{
                                address_line_1: this.selectedAddressEnvio.address,
                                country: "CO",
                                region: this.selectedAddressEnvio.state,
                                city: this.selectedAddressEnvio.city,
                                name: this.userData.name,
                                phone_number: this.userData.phone ? this.userData.phone : this.addressPrincipal.phone ? this.addressPrincipal.phone : '+573007819686',
                                postal_code: this.selectedAddressEnvio.postal_code
                            },
                            payment_method: {
                                type: "PSE",
                                user_type: this.personTypeSelected, // Tipo de persona, natural (0) o jurídica (1)
                                user_legal_id_type: this.userData.documentType.replace(/[^\w\s]/gi, ''), // Tipo de documento, CC o NIT
                                user_legal_id: this.userData.documentNumber, // Número de documento
                                financial_institution_code: this.bancoSelected, // Código (`code`) de la institución financiera
                                payment_description: 'Pago de productos de aloranges',
                            },
                        }
                        try {
                            result = await this.call_api('POST','product/payment-wompi-pse',data);
                            let idTransaction = result.data.PaymentResult.data.id;
                            if(idTransaction){
                                let dataToTransaction = {
                                    id: idTransaction,
                                };   
                                let resultURL = await this.verifyStatusPayment(dataToTransaction);
                                if (typeof resultURL === 'string') {
                                    this.urlPagoPSE = resultURL;
                                    let formData = this.processToSendStore(referenceToPayment);
                                    const res = await this.call_api("post", "checkout/order/store", formData);
                                    this.dataCheckout = res.data;
                                }
                            }                            
                        } catch (error) {
                            this.snack({
                                message: 'Algo ha salido mal, intenta nuevamente mas tarde',
                                color: "red"
                            });  
                            console.log(error); 
                        }
                    }else if(this.pick === 5){
                        if(this.isEfectivo == false && this.isDatafono == false){
                            this.snack({
                                message: 'Debe elegir un medio de pago "Pago con Efectivo" o "Pago con Datafono" ',
                                color: "red"
                            });

                            this.checkoutLoading = false;
                        }else{
                            let formData = this.processToSendStore(referenceToPayment);
                            const res = await this.call_api("post", "checkout/order/store", formData);
                            this.dataCheckout = res.data;
                            this.numberPag = 4;
                        }   
                    }
                    else{
                        let formData = this.processToSendStore(referenceToPayment);
                        const res = await this.call_api("post", "checkout/order/store", formData);
                        this.dataCheckout = res.data;
                        this.numberPag = 4;
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
        this.getInfoUser();
    }
};
</script>

<style lang="scss" scoped>
::v-deep {
    .v-stepper__header {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    // .v-stepper__step {
    //     padding: 2px;
    //     flex: 1;
    // }

    // .v-stepper__header {
    //     height: 40px;
    //     box-shadow: none;
    // }

     .v-stepper__content {
        padding: 24px 0px 16px !important;
         //padding: 0;
     }

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
        background-color: #03be41 !important;
        border-color: #03be41;
    }
    .v-timeline-item__inner-dot {
        background-color: #03be41 !important;
        border-color: #03be41 !important;
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

.hide-button-check {
    position: fixed;
    opacity: 0;
    pointer-events: none;
}
::v-deep.cartSteper {
    &-card {
        border-radius: 10px;
    }

    .v-stepper__step--complete {
        background: #606161;
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
            grid-template-columns: 6fr 4fr 1fr 1fr;

            @media (min-width: 768px) {
                grid-template-columns: 5fr 2fr 2fr 3fr;
                padding: 0.75rem 0;
            }

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
    background-color: transparent;
    margin-top: 7%;
    border-radius: 10px;
}
.text-cartempty {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 0;
}
.img-cartempty {
    width: 45%;
    margin: auto;
}

// Fin estilos //
.container {
    background-color: #fafcfc;
}

.form {
    border: 1px solid #d5d6d9;
    box-shadow: -2px -2px 5px rgba(0, 0, 0, 0.1), 2px 2px 5px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 10px;
    & .mb-2 {
        margin-bottom: 15px !important;
    }
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
    top: 40px;
    left: 15px;
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

.cartSteper-step {
    .v-stepper__step--complete {
        background-color: #9b9c9c;
    }
}

.v-stepper__header {
    ::v-deep {
        .v-stepper__step--complete {
            .v-stepper__step__step {
                background-color: #9b9c9c;
            }
        }
    }
}

// TOOLTIP CREADO A MANO

.tooltip {
    display: none;
    position: absolute;
    background-color: black;
    color: #fff;
    padding: 10px;
    border-radius: 5px;
    z-index: 1;
}

#customButton {
    position: relative;
}

#customButton:hover + .tooltip {
    display: block;
}

.tooltip:hover {
    display: block;

    .boton-guardar {
        font-size: var(--font-size-btn);
        font-weight: 600;
        letter-spacing: 2;
        transition: all 0.2s ease-in-out;
        color: #fff !important;
        background-color: rgba(#000000, 0.8) !important;
    }
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
        background-color: black;
    }

    &:focus,
    &.v-btn--active {
        color: #fff;
        background-color: black;
    }
}

.selector {
    &::v-deep {
        .v-input__slot {
            background: #dfdfdf !important;

            &:hover {
                background: #dfdfdf;
            }
        }
    }
}

.finalizar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    place-items: center;

    @media screen and (max-width: 602px) {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        place-items: center;
    }
}

.seguimiento {
    display: flex;
    justify-content: flex-end;
    place-items: center;
    align-items: center;
    @media screen and (max-width: 821px) {
        padding-right: 5px;
    }
}

@media screen and (max-width: 821px) {
    .tamaño-responsive {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
}

.lista-pedido {
    width: 99%;
    @media screen and (max-width: 821px) {
        width: 100%;
    }
}

.ojo {
    display: flex;
    align-items: center;
}

.icono-cantidad {
    padding: 5px;
    background: white;
    border: 1px solid #e7e7e7;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icono-exclamacion {
    border: 1 px solid #e7e7e7;
    border-radius: 6px;
    background-color: #ffff;
}

.barra-inferior {
    // position: fixed;
    bottom: 0;
    background-color: #f4f5f7;
    margin-bottom: 0 !important;
    // @media screen and (min-width: 820px) {
    //     width: 75%;
    // }
}

.detalles-final {
    &-total {
        justify-content: flex-start;
        display: flex;
        align-items: center;
    }
    &-details {
        justify-content: flex-end;
        display: flex;
        align-items: center;
    }
}

.espaciado {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

@media screen and (max-width: 600px) {
    .body2 {
        padding-right: 10px;
    }
}

@media screen and (max-width: 959px) {
    .boton-continuar {
        width: 100%;
    }
}

.gap {
    gap: 5px;
    @media screen and (min-width: 440px) {
        gap: 25px;
    }
}

.gap-1 {
    gap: 25px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    @media screen and (min-width: 440px) {
        gap: 70px;
    }
}
.active-payment {
    background-color: #ffefdf;
    border: 1px solid #f58634;
    border-radius: 10px;
}
.bold {
    font-weight: bold;
}
.custom-radio {
    display: inline-flex;
    align-items: center;
    cursor: pointer;

    input[type="radio"] {
        display: none;
    }

    .radio-check {
        width: 20px;
        height: 20px;
        border: 2px solid #f58634;
        border-radius: 50%;
        position: relative;
        display: inline-block;
        margin-right: 8px;
    }

    input[type="radio"]:checked + .radio-check {
        background-color: #f58634;
    }

    .radio-check::after {
        content: "";
        width: 10px;
        height: 10px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    input[type="radio"]:checked + .radio-check::after {
        opacity: 1;
    }
}

.data-payments {
    width: 100%;
    @media (min-width: 600px) {
        width: 70%;
    }
}
.data-section {
    margin-bottom: 20px; /* Espaciado entre los cuadros */
    //   box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
    border: 1px solid #ececec;
}

.data-section h3 {
    background-color: #ececec;
    font-size: 16px; /* Tamaño del título */
    padding: 10px;
}

.data-section .body-data {
    padding: 10px;
}
.data-section .body-data p {
    margin: 2px 0; /* Espaciado entre las líneas de texto */
    font-size: 18px; /* Tamaño del texto */
    color: #333; /* Color del texto */
}
h5{
    font-size: 25px
}
</style>
