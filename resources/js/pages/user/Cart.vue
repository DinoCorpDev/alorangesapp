<template>
    <div>
        <v-stepper elevation="0" v-model="numberPag">
            <v-stepper-header>
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
                <v-stepper-content class="tamaño-responsive" step="1" style="overflow-y: scroll; max-height: 450px">
                    <template v-if="cartProducts.length > 0">
                        <div class="cart-table-header mb-2" style="width: 100%">
                            <div style="padding-left: 4%">Productos</div>
                            <div>Precio</div>
                            <div class="d-none d-md-flex">Cantidad</div>
                            <div class="d-flex d-md-none">Cant</div>
                            <div class="d-none d-sm-flex">Opciones</div>
                            <div class="d-flex d-sm-none">Opc</div>
                        </div>
                        <v-row no-gutters class="car-items" style="">
                            <v-col cols="12" v-for="(product, i) in cartProducts" :key="i">
                                <ProductCart :productDetails="product" productCartType="checkout" />
                            </v-col>
                        </v-row>

                        <v-row class="barra-inferior">
                            <v-col
                                cols="12"
                                md="9"
                                class="d-flex justify-center justify-md-end align-center pl-0 pl-md-3"
                                style="gap: 25px"
                            >
                                <total :total="priceTotal" />
                                <v-divider vertical></v-divider>
                                <div class="icono-cantidad">
                                    <CustomFavorite />
                                    <span style="color: #919191; margin-left: 10px">{{ getCartCount }}</span>
                                </div>
                                <v-divider vertical></v-divider>
                                <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
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
                                </CustomButton>
                            </v-col>
                            <v-col cols="12" md="3" class="d-flex justify-end align-center pl-0 pl-md-3">
                                <div class="boton-continuar" v-if="mostrarDetalles == false">
                                    <CustomButton
                                        class="boton-continuar"
                                        text="Continuar >"
                                        color="grey2"
                                        @click="numberPag = 2"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12" style="justify-content: center" v-if="mostrarDetalles">
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Descuento detalle
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Recargo detalle
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0" style="font-size: 15px">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            TOTAL BRUTO FACTURA
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold" style="font-size: 15px"
                                            >000.000.000 COP</span
                                        >
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            IVA
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Otros impuestos
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start; font-size: 15px"
                                        >
                                            TOTAL IMPUESTO
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold" style="font-size: 15px"
                                            >000.000.000 COP</span
                                        >
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete ICA
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete IVA
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start"
                                        >
                                            Rete fuente
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
                                        <span class="body2 font-weight-bold">000.000.000 COP</span>
                                    </v-col>
                                </v-row>
                                <v-row style="justify-content: center">
                                    <v-divider style="max-width: 60%; margin-right: 82px" />
                                </v-row>
                                <v-row>
                                    <v-col cols="2" />
                                    <v-col cols="3" class="py-0">
                                        <span
                                            class="body2 font-weight-bold py-0"
                                            style="display: flex; justify-content: flex-start; font-size: 15px"
                                        >
                                            TOTAL RETENCIONES
                                        </span>
                                    </v-col>
                                    <v-col cols="4" class="seguimiento py-0">
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
                                        color="nero"
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
                                    <h5 class="fw-600">Dirección de envío</h5>
                                    <v-divider class="my-4" />
                                    <div class="form mb-5">
                                        <v-row>
                                            <v-col cols="11">
                                                <SelectCustom
                                                    :dark="darkBoxes"
                                                    label="Usuario Principal"
                                                    :items="addressesParaEnvio"
                                                    @input="changeAddress($event, 0)"
                                                    item-text="address"
                                                    item-value="id"
                                                    placeholder="Seleccione una opcion"
                                                />
                                            </v-col>
                                            <v-col cols="1" class="pl-0 ojo">
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
                                        <SelectCustom
                                            :dark="darkBoxes"
                                            label="Usuario Principal"
                                            :items="addressesParaEnvio"
                                            @input="changeAddress($event, 0)"
                                            item-text="address"
                                            item-value="id"
                                            placeholder="Seleccione una opcion"
                                        />
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">--</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1">{{
                                                selectedAddressEnvio?.address ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold"> Dirección adicional </span>
                                            <span class="body1">{{
                                                selectedAddressEnvio?.address ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{
                                                selectedAddressEnvio?.postal_code ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{
                                                selectedAddressEnvio?.country ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{
                                                selectedAddressEnvio?.city ||
                                                "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Localidad</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1">
                                                {{
                                                    selectedAddressEnvio?.neighborhood ||
                                                    "No registra" | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{
                                                addressPrincipal?.phone | filtroParaOcultarInfo(mostrarDatosEnvio)
                                            }}</span>
                                        </div>
                                        <CustomButton
                                            v-if="Object.entries(addressPrincipal).length !== 0"
                                            class="mr-3"
                                            color="grey"
                                            text="EDITAR"
                                            @click="editAddress(addressPrincipal, 'shipping')"
                                        />
                                        <CustomButton
                                            v-if="Object.entries(addressPrincipal).length === 0"
                                            block
                                            color="grey"
                                            text="AÑADIR"
                                            @click="openAdress('shipping')"
                                        />
                                    </div>
                                    <div class="form">
                                        <h6 class="black--text bold">Encargado</h6>
                                        <v-divider class="my-3" />
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">NOMBRE COMPLETO</span>
                                            <span class="body1">{{ currentUser.name || "--" }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-3">
                                            <span class="subtitle1 text-uppercase bold">TELÉFONO / CELULAR</span>
                                            <span class="body1">--</span>
                                        </div>
                                        <CustomButton
                                            color="grey"
                                            class="mr-3"
                                            text="EDITAR"
                                            @click="showFormEncargado"
                                        />
                                        <template v-if="(mostrarFormEncargado = true)">
                                            <Encargado @ocultar-form-encargado="showFormEncargado" />
                                        </template>
                                    </div>
                                </v-col>
                                <v-col cols="12"> </v-col>
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
                                    <h5 class="fw-600">Dirección de servicio</h5>
                                    <v-divider class="my-3" />
                                    <div class="form">
                                        <div
                                            v-if="
                                                Object.entries(addressServicio).length !== 0 &&
                                                useDefaultAddress1 == false
                                            "
                                        >
                                            <v-row>
                                                <v-col cols="11">
                                                    <SelectCustom
                                                        :dark="darkBoxes"
                                                        label="Ingrese una direccion"
                                                        :items="addressesParaServicio"
                                                        @input="changeAddress($event, 1)"
                                                        item-text="address"
                                                        item-value="id"
                                                        placeholder="Seleccione una opcion"
                                                    />
                                                </v-col>
                                                <v-col cols="1" class="pl-0 ojo">
                                                    <i
                                                        class="las la-eye-slash"
                                                        style="font-size: 25px"
                                                        v-if="mostrarDatosServicio"
                                                        @click="toggleDatosServicio"
                                                    ></i>
                                                    <i
                                                        class="las la-eye"
                                                        style="font-size: 25px"
                                                        @click="toggleDatosServicio"
                                                        v-else
                                                    ></i>
                                                </v-col>
                                            </v-row>
                                            <v-divider class="my-3" />
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">país</span>
                                                <span class="body1">--</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                                <span class="body1">{{
                                                    addressServicio?.name | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                                <span class="body1 text-right">{{
                                                    addressServicio?.address
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold"> Dirección adicional </span>
                                                <span class="body1">--</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                                <span class="body1">{{
                                                    addressServicio?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                                <span class="body1">{{
                                                    addressServicio?.country
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                                <span class="body1">{{
                                                    addressServicio?.city | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">localidad</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.phone
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                        </div>
                                        <label class="label my-3">
                                            <input
                                                type="checkbox"
                                                v-model="useDefaultAddress1"
                                                id="useDefaultAddress1"
                                            />
                                            <span class="body-1 black--text text">
                                                Usar la misma Dirección de envió para que Idovela preste los servicio de
                                                instalación, mantenimiento y más.
                                            </span>
                                            <span class="checkmark"></span>
                                        </label>
                                        <v-divider class="my-3" />
                                        <CustomButton
                                            v-if="
                                                Object.entries(addressServicio).length !== 0 &&
                                                useDefaultAddress1 == false
                                            "
                                            class="mr-3"
                                            color="grey"
                                            text="EDITAR"
                                            @click="editAddress(addressServicio, 'service')"
                                        />
                                        <CustomButton
                                            v-if="
                                                Object.entries(addressServicio).length === 0 &&
                                                useDefaultAddress1 == false
                                            "
                                            block
                                            color="grey"
                                            text="Añadir Dirección"
                                            @click="openAdress('service')"
                                        />
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
                                    class="d-none d-md-flex justify-start align-center"
                                    cols="2"
                                    style="display: flex"
                                >
                                    <div v-if="mostrarDetalles == false">
                                        <CustomButton text="< Volver" color="nero" @click="numberPag = 1" />
                                    </div>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="8"
                                    class="d-flex justify-center justify-md-start align-center"
                                    style="gap: 25px"
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
                                    <v-divider vertical></v-divider>
                                    <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
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
                                    </CustomButton>
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
                                        <v-col cols="6" md="12">
                                            <div v-if="mostrarDetalles == false" class="boton-continuar">
                                                <CustomButton
                                                    class="boton-continuar"
                                                    text="Continuar >"
                                                    color="grey2"
                                                    @click="numberPag = 3"
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
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Envío de mercancia
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Movilidad de servicio
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col
                                            class="boton-continuar"
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
                                            class="boton-continuar"
                                            style="display: flex; justify-content: flex-end; align-items: center"
                                        >
                                            <CustomButton
                                                class="boton-continuar"
                                                text="Continuar >"
                                                color="grey2"
                                                @click="numberPag = 3"
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
                        <v-col cols="12" md="6" order="2" order-md="1" order-sm="1">
                            <h5 class="fw-600">Seleccionar medio de pago</h5>
                            <v-divider class="my-4" />
                            <div class="form">
                                <v-row>
                                    <v-col>
                                        <TypePayment img="/public/assets/img/pse.png" text="Pse" />
                                        <input
                                            type="radio"
                                            name="medio-de-pago"
                                            autocomplete="off"
                                            v-model="pick"
                                            :value="1"
                                        />
                                    </v-col>
                                    <v-col>
                                        <TypePayment img="/public/assets/img/credito.png" text="Credito" />
                                        <input
                                            type="radio"
                                            name="medio-de-pago"
                                            autocomplete="off"
                                            v-model="pick"
                                            :value="2"
                                        />
                                    </v-col>
                                    <v-col>
                                        <TypePayment img="/public/assets/img/debito.png" text="Debito" />
                                        <input
                                            type="radio"
                                            name="medio-de-pago"
                                            autocomplete="off"
                                            v-model="pick"
                                            :value="3"
                                        />
                                    </v-col>
                                    <v-col>
                                        <TypePayment img="/public/assets/img/efecty.png" text="Efecty" />
                                        <input
                                            type="radio"
                                            name="medio-de-pago"
                                            autocomplete="off"
                                            v-model="pick"
                                            :value="5"
                                        />
                                    </v-col>
                                    <v-col>
                                        <div class="d-none d-sm-flex">
                                            <TypePayment img="/public/assets/img/transferir.png" text="Transferir" />
                                        </div>
                                        <div class="d-flex d-sm-none">
                                            <TypePayment img="/public/assets/img/transferir.png" text="Transf" />
                                        </div>
                                        <input type="radio" name="medio-de-pago" autocomplete="off" :value="4" />
                                    </v-col>
                                </v-row>
                                <v-divider class="my-3" />
                                <div v-if="pick === 1">
                                    <label class="text-uppercase">Tipo de Persona</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Tipo de persona"
                                        :items="langSelectItems"
                                    />
                                    <label class="text-uppercase">Banco</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Seleccionar banco"
                                        :items="langSelectItems"
                                    />
                                </div>
                                <div v-if="pick === 2">
                                    <label class="text-uppercase">Numero de tarjeta</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Nombre titular de la tarjeta</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Fecha de expedicion</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Tipo de persona"
                                        :items="langSelectItems"
                                    />
                                    <label class="text-uppercase">Codigo de seguridad</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Numero de CVV2</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Documento</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Seleccionar banco"
                                        :items="langSelectItems"
                                    />
                                    <label class="text-uppercase">Numero de documento</label>
                                    <CustomInput />
                                </div>
                                <div v-if="pick === 3">
                                    <label class="text-uppercase">Numero de tarjeta</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Nombre titular de la tarjeta</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Fecha de expedicion</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Tipo de persona"
                                        :items="langSelectItems"
                                    />
                                    <label class="text-uppercase">Codigo de seguridad</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Numero de CVV2</label>
                                    <CustomInput />
                                    <label class="text-uppercase">Documento</label>
                                    <SelectCustom
                                        class="selector"
                                        :dark="darkBoxes"
                                        label="Seleccionar banco"
                                        :items="langSelectItems"
                                    />
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
                                        style="display: none"
                                        ref="fileInput"
                                        type="file"
                                        @change="fileSelected"
                                        enctype="multipart/form-data"
                                    />
                                </div>
                                <CustomButton v-if="pick != 0" class="mt-2" text="Aplicar" color="grey" />
                            </div>
                        </v-col>
                        <v-col cols="12" md="6" order="1" order-md="2" order-sm="2">
                            <h5 class="fw-600">Facturar a nombre de</h5>
                            <v-divider class="my-4" />
                            <div class="form">
                                <v-row>
                                    <v-col cols="11">
                                        <SelectCustom
                                            class="selector"
                                            label="Usuario Principal"
                                            :items="langSelectItems"
                                        />
                                    </v-col>
                                    <v-col cols="1" class="pl-0 ojo">
                                        <i
                                            class="las la-eye-slash"
                                            style="font-size: 25px"
                                            v-if="mostrarDatosFacturacion"
                                            @click="toggleDatosFacturacion"
                                        ></i>
                                        <i
                                            class="las la-eye"
                                            style="font-size: 25px"
                                            @click="toggleDatosFacturacion"
                                            v-else
                                        ></i>
                                    </v-col>
                                </v-row>
                                <v-divider class="my-3" />
                                <div
                                    v-if="
                                        Object.entries(addressFacturacion).length !== 0 && useDefaultAddress2 == false
                                    "
                                >
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">correo electrónico</span>
                                        <span class="body1">--</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Tipo de persona</span>
                                        <span class="body1 text-right">--</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                        <span class="body1">{{
                                            addressFacturacion?.address | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                        }}</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Tipo de documento</span>
                                        <span class="body1">--</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Número de documento</span>
                                        <span class="body1">--</span>
                                    </div>
                                </div>
                                <!-- <label class="label my-3">
                                    <input type="checkbox" v-model="useDefaultAddress2" id="useDefaultAddress2" />
                                    <span class="body-1 black--text text">
                                        Usar la misma Dirección de envió para que Idovela entreguela factura física.
                                    </span>
                                    <span class="checkmark"></span>
                                </label>
                                <v-divider class="my-3" />
                                <CustomButton
                                    v-if="
                                        Object.entries(addressFacturacion).length !== 0 && useDefaultAddress2 == false
                                    "
                                    class="mr-3"
                                    color="grey"
                                    text="Editar"
                                    @click="editAddress(addressFacturacion, 'billing')"
                                />
                                <CustomButton
                                    v-if="
                                        Object.entries(addressFacturacion).length === 0 && useDefaultAddress2 == false
                                    "
                                    block
                                    color="grey"
                                    text="Añadir Dirección"
                                    @click="openAdress('billing')"
                                /> -->
                            </div>
                        </v-col>
                        <v-col cols="12" md="6" order="3" order-md="3" order-sm="3">
                            <h5 class="fw-600">Codigo promocional</h5>
                            <v-divider class="my-4" />
                            <div class="form">
                                <v-row>
                                    <v-col cols="3" style="display: flex; place-items: center">
                                        <Promocion />
                                    </v-col>

                                    <v-col cols="9" style="display: flex; place-items: center">
                                        <p class="mb-0">
                                            Si tienes un código promocional, cupón de descuento o un promocional
                                            agrégalo
                                        </p>
                                    </v-col>
                                    <CustomButton class="mb-4" block color="grey" text="Añadir código" />
                                </v-row>
                            </div>
                        </v-col>
                        <v-col cols="12" order="3" order-md="3" order-sm="3" class="d-flex justify-space-between">
                            <v-row class="barra-inferior">
                                <v-col cols="2" style="display: flex; justify-content: flex-start; align-items: center">
                                    <div v-if="mostrarDetalles == false">
                                        <CustomButton text="< Volver" color="nero" @click="numberPag = 2" />
                                    </div>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="8"
                                    style="display: flex; justify-content: flex-start; gap: 70px; align-items: center"
                                >
                                    <TotalPago :total="priceTotal" />

                                    <v-divider vertical></v-divider>
                                    <CustomButton color="black" v-if="mostrarDetalles" @click="ocultarDetalle">
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
                                    </CustomButton>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="2"
                                    style="display: flex; justify-content: flex-end; align-items: center"
                                >
                                    <div v-if="mostrarDetalles == false">
                                        <CustomButton
                                            text="Continuar"
                                            color="nero"
                                            @click="proceedCheckout()"
                                            :loading="checkoutLoading"
                                            :disabled="checkoutLoading"
                                        />
                                    </div>
                                </v-col>
                                <v-col
                                    cols="12"
                                    style="justify-content: center; max-height: 300px; overflow-y: scroll"
                                    v-if="mostrarDetalles"
                                >
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="4" style="display: flex; justify-content: flex-start; gap: 10px">
                                            <total :total="priceTotal" />
                                            <v-divider vertical></v-divider>
                                            <div class="icono-cantidad">
                                                <CustomFavorite />
                                                <span style="color: #919191; margin-left: 10px">{{
                                                    getCartCount
                                                }}</span>
                                            </div>
                                        </v-col>
                                        <v-col cols="3" />
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Descuento detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL BRUTO FACTURA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL IMPUESTO
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete ICA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                TOTAL RETENCIONES
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold" style="font-size: 15px"
                                                >000.000.000 COP</span
                                            >
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="6" style="display: flex; justify-content: flex-start; gap: 10px">
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
                                        <v-col cols="1" />
                                    </v-row>

                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>

                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Envío de mercancia
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Movilidad de servicio
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Rete fuente
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                IVA
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start"
                                            >
                                                Recargo detalle
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
                                    <v-row style="justify-content: center">
                                        <v-divider style="max-width: 60%; margin-right: 82px" />
                                    </v-row>
                                    <v-row>
                                        <v-col cols="2" />
                                        <v-col cols="3" class="py-0">
                                            <span
                                                class="body2 font-weight-bold py-0"
                                                style="display: flex; justify-content: flex-start; font-size: 15px"
                                            >
                                                Otros impuestos
                                            </span>
                                        </v-col>
                                        <v-col cols="4" class="seguimiento py-0">
                                            <span class="body2 font-weight-bold">000.000.000 COP</span>
                                        </v-col>
                                    </v-row>
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
                            <div class="div-alert">
                                <div class="information">
                                    <v-row>
                                        <v-col
                                            cols="2"
                                            sm="1"
                                            style="
                                                display: flex;
                                                justify-content: center;
                                                align-items: center;
                                                place-items: center;
                                            "
                                        >
                                            <div>
                                                <span class="success"><i class="las la-check"></i></span>
                                            </div>
                                        </v-col>

                                        <v-col cols="10" sm="8">
                                            <h6 class="font-weight-bold">Gracias por registrarse</h6>
                                            <p class="body-1 mb-0" style="margin-top: 4px">
                                                Enviaremos al e-mail de facturación un correo de verificación por la
                                                compra
                                            </p>
                                        </v-col>

                                        <v-col class="finalizar" cols="12" sm="3">
                                            <CustomButton
                                                text="FINALIZAR"
                                                width="150"
                                                color="nero"
                                                @click="numberPag = 3"
                                            />
                                        </v-col>

                                        <div>
                                            <v-row> </v-row>
                                        </div>
                                    </v-row>
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
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="form">
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
                                <a href="#" class="black--text text-decoration-underline font-weight-bold">
                                    Conocer la logistica de envió
                                </a>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="form">
                                <h6 class="black--text bold">Seguimiento de servicio</h6>
                                <v-divider class="my-3" />
                                <v-timeline dense>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">Comprado</span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">jueves 07, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">Agendamiento</span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">jueves 07, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col>
                                                <span class="body2 text-uppercase font-weight-bold">
                                                    En preparación
                                                </span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">jueves 07, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                    <v-timeline-item color="black" small fill-dot>
                                        <v-row>
                                            <v-col cols="7">
                                                <span class="body2 text-uppercase font-weight-bold">
                                                    Verificación de adecuación
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
                                                <span class="body2 text-uppercase font-weight-bold">
                                                    Instalación concluida
                                                </span>
                                            </v-col>
                                            <v-col class="seguimiento">
                                                <span class="body2 font-weight-bold">jueves 07, abril</span>
                                            </v-col>
                                        </v-row>
                                    </v-timeline-item>
                                </v-timeline>
                                <a href="#" class="black--text text-decoration-underline font-weight-bold">
                                    Conocer la logistica de envió
                                </a>
                            </div>
                        </v-col>
                    </v-row>
                    <div class="my-5">
                        <h5 class="fw-600">Lista de Pedido</h5>
                        <v-divider class="my-4" style="margin-bottom: 34px !important" />

                        <v-row class="lista-pedido">
                            <div class="cart-table-header mb-2" style="width: 100%">
                                <div style="padding-left: 4%">Productos</div>
                                <div>Precio</div>
                                <div class="d-none d-md-flex">Cantidad</div>
                                <div class="d-flex d-md-none">Cant</div>
                                <div class="d-none d-sm-flex">Opciones</div>
                                <div class="d-flex d-sm-none">Opc</div>
                            </div>
                            <v-col cols="12" v-for="(product, i) in cartItems" :key="i">
                                <ProductCart
                                    :productDetails="product"
                                    @changeQty="changeQty"
                                    @changeQtyMinus="changeQty"
                                />
                            </v-col>
                        </v-row>
                        <v-divider class="my-5" />
                        <v-row>
                            <v-col cols="12" sm="4" class="pa-0" />
                            <v-col cols="8" sm="5" class="detalles-final">
                                <!-- HOLA HOLA HOLA-->
                                <TotalPago :total="priceTotal" />
                            </v-col>
                            <v-col cols="1" class="d-none d-sm-flex detalles-final">
                                <v-divider vertical></v-divider>
                            </v-col>
                            <v-col cols="4" sm="2" class="detalles-final">
                                <CustomButton color="black" v-if="mostrarDetallesFinal" @click="ocultarDetalleFinal">
                                    DETALLES
                                    <i
                                        class="las la-sort-down"
                                        style="color: white; font-size: 20px; padding-left: 10px; padding-bottom: 5px"
                                    >
                                    </i>
                                </CustomButton>
                                <CustomButton color="grey2" @click="mostrarDetalleFinal" v-else>
                                    DETALLES
                                    <i class="las la-sort-up" style="color: black; font-size: 20px; padding-left: 10px">
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
                                            <span style="color: #919191; margin-left: 10px">{{ getCartCount }}</span>
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
                    <v-divider class="mb-3" />
                    <v-row>
                        <v-col cols="12" md="6">
                            <h5 class="fw-600">Dirección de envío</h5>
                            <v-divider class="my-4" />
                            <v-row>
                                <v-col cols="12">
                                    <div class="form">
                                        <v-row>
                                            <v-col cols="11">
                                                <h5 class="black--text">Variable nombre de dirección *</h5>
                                            </v-col>
                                            <v-col cols="1" class="pl-0 ojo">
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
                                                <span class="body1 text-right">{{
                                                    addressFacturacion?.address
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Descripción de Dirección
                                                </span>
                                                <span class="body1">{{
                                                    addressFacturacion?.address
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                                <span class="body1">{{
                                                    addressFacturacion?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                                <span class="body1">{{
                                                    addressFacturacion?.country
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                                <span class="body1">{{
                                                    addressFacturacion?.city | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.phone | filtroParaOcultarInfo(mostrarDatosEnvio)
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
                                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                                <span class="body1">Dirección principal</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                                <span class="body1 text-right">{{
                                                    addressPrincipal?.address | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Descripción de Dirección
                                                </span>
                                                <span class="body1">{{
                                                    addressPrincipal?.address | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.country | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.city | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Nombre de quien lo va a recibir
                                                </span>
                                                <span class="body1">{{
                                                    addressPrincipal?.name | filtroParaOcultarInfo(mostrarDatosEnvio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                                <span class="body1">{{
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
                                    <div class="form">
                                        <h5 class="black--text">Encargado</h5>
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
                                    <h5 class="fw-600">Dirección de servicio</h5>
                                    <v-divider class="my-4" />
                                    <div class="form">
                                        <v-row>
                                            <v-col cols="11">
                                                <h5 class="black--text">Nombre de dirección servicio *</h5>
                                            </v-col>
                                            <v-col cols="1" class="pl-0 ojo">
                                                <i
                                                    class="las la-eye-slash"
                                                    style="font-size: 25px"
                                                    v-if="mostrarDatosServicio"
                                                    @click="toggleDatosServicio"
                                                ></i>
                                                <i
                                                    class="las la-eye"
                                                    style="font-size: 25px"
                                                    @click="toggleDatosServicio"
                                                    v-else
                                                ></i>
                                            </v-col>
                                        </v-row>
                                        <v-divider class="my-3" />

                                        <div
                                            v-if="
                                                Object.entries(addressServicio).length !== 0 &&
                                                useDefaultAddress1 == false
                                            "
                                        >
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                                <span class="body1">Dirección principal</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                                <span class="body1">{{
                                                    addressServicio?.address
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Descripción de Dirección
                                                </span>
                                                <span class="body1">{{
                                                    addressServicio?.address
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                                <span class="body1">{{
                                                    addressServicio?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                                <span class="body1">{{
                                                    addressServicio?.country
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                                <span class="body1">{{
                                                    addressServicio?.city | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.phone
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                        </div>

                                        <div
                                            class=""
                                            v-if="
                                                Object.entries(addressPrincipal).length !== 0 &&
                                                useDefaultAddress1 == true
                                            "
                                        >
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                                <span class="body1">Dirección principal</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                                <span class="body1 text-right">{{
                                                    addressPrincipal?.address
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Descripción de Dirección
                                                </span>
                                                <span class="body1">{{
                                                    addressPrincipal?.address
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.postal_code
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.country
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.city | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                                <span class="body1"> -- </span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">
                                                    Nombre de quien lo va a recibir
                                                </span>
                                                <span class="body1">{{
                                                    addressPrincipal?.name | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-space-between mb-2">
                                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                                <span class="body1">{{
                                                    addressPrincipal?.phone
                                                        | filtroParaOcultarInfo(mostrarDatosServicio)
                                                }}</span>
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
                            </v-row>
                        </v-col>
                        <v-col cols="12" md="6">
                            <h5 class="fw-600">Facturar a nombre de</h5>
                            <v-divider class="my-4" />
                            <v-row>
                                <v-col cols="12">
                                    <div class="form">
                                        <v-row>
                                            <v-col cols="11">
                                                <h5 class="black--text">Usuario principal</h5>
                                            </v-col>
                                            <v-col cols="1" class="pl-0 ojo">
                                                <i
                                                    class="las la-eye-slash"
                                                    style="font-size: 25px"
                                                    v-if="mostrarDatosFacturacion"
                                                    @click="toggleDatosFacturacion"
                                                ></i>
                                                <i
                                                    class="las la-eye"
                                                    style="font-size: 25px"
                                                    @click="toggleDatosFacturacion"
                                                    v-else
                                                ></i>
                                            </v-col>
                                        </v-row>

                                        <v-divider class="my-3" />
                                        <SelectCustom
                                            class="selector"
                                            :dark="darkBoxes"
                                            label="Usuario Principal"
                                            :items="langSelectItems"
                                        />
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                            <span class="body1">Dirección principal</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Dirección</span>
                                            <span class="body1 text-right">{{
                                                addressPrincipal?.address
                                                    | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Descripción de Dirección
                                            </span>
                                            <span class="body1">{{
                                                addressPrincipal?.address
                                                    | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                            <span class="body1">{{
                                                addressPrincipal?.postal_code
                                                    | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Departamento</span>
                                            <span class="body1">{{
                                                addressPrincipal?.country
                                                    | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Municipio</span>
                                            <span class="body1">{{
                                                addressPrincipal?.city | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Barrio</span>
                                            <span class="body1"> -- </span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">
                                                Nombre de quien lo va a recibir
                                            </span>
                                            <span class="body1">{{
                                                addressPrincipal?.name | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                        <div class="d-flex justify-space-between mb-2">
                                            <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                            <span class="body1">{{
                                                addressPrincipal?.phone | filtroParaOcultarInfo(mostrarDatosFacturacion)
                                            }}</span>
                                        </div>
                                    </div>
                                </v-col>

                                <v-col cols="12">
                                    <h5 class="fw-600">Medio de pago</h5>
                                    <v-divider class="my-4" />
                                    <div class="form">AQUÍ VA EL MEDIO DE PAGO CON LA RESPECTIVA INFORMACIÓN*</div>
                                    <!-- <div class="form">
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
                                    </div> -->
                                </v-col>
                                <v-col cols="12">
                                    <h5 class="fw-600">Código promocional</h5>
                                    <v-divider class="my-4" />
                                    <div class="form">
                                        <div class="d-flex"><regalo /></div>
                                        <v-divider class="my-4" />
                                        <v-row>
                                            <v-col cols="6" class="text-left"> CODIGO (REGALO/REFERIDO) </v-col>
                                            <v-col cols="6" class="text-right"> XXXX XXXX XXXX XXXX </v-col>
                                        </v-row>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>

                    <v-divider class="my-4" />

                    <v-row>
                        <p style="font-size: 0.7rem; color: #b4b5b5; padding-left: 1.2rem; text-align: left">
                            Protección de datos personales: IDOVELA S.A.S garantiza el tratamiento de datos personales
                            acorde a lo establecido en la ley 1581/2012 y decreto 1377/2013. por favor dirija sus
                            inquietudes al correo: soporte@idovela.com Riesgo de lavado de activos, financiación al
                            terrorismo sarlaft y ley antisoborno: EL CLIENTE certifica a IDOVELA S.A.S que sus recursos
                            no provienen ni se destinan al ejercicio de ninguna actividad ilícita o de actividades
                            conexas al lavado de activos provenientes de estas o de actividades relacionadas con la
                            financiación del terrorismo EL CLIENTE se obliga a realizar todas las actividades
                            encaminadas a asegurar que los recursos de estos, no se encuentran relacionados, provengan,
                            de actividades ilícitas, particularmente de lavado de activos o financiación del terrorismo.
                            Igualmente se compromete a respetar el programa de ética empresarial que aplica en IDOVELA
                            S.A.S. Garantía: 1). Garantiza el articulo a partir de la fecha prescrita. únicamente
                            garantía de fábrica. 2). Garantizamos el articulo según nos ofrecen nuestro(s)
                            proveedor(es). 3). Situaciones no cubiertas por la garantía legal: • Productos cuyo periodo
                            de garantía haya expirado o finalizado. • Situaciones ocasionadas por fuerza mayor, caso
                            fortuito, culpa exclusiva del usuario o terceros • Problemas causados como consecuencia de
                            instalaciones físicas y eléctricas inadecuadas, uso o mantenimiento inadecuados o diferentes
                            al indicado en el manual del producto, por personal no autorizado por iDOVELA S.A.S. Cambios
                            y devoluciones: después de 30 días fecha factura, para cambios por favor presentar la
                            factura original en horario de lunes a sábado de 8:00 am a 5:00 pm. la mercancía se recibe
                            en perfecto estado, caja sellada, el material en calidad de segunda (uso o deterioro) no
                            tiene cambios. no devolvemos dinero Logística: la mercancía se deja hasta dónde llega el
                            camión (primera planta). una vez recibida y firmada esta factura se entenderá que la
                            mercancía fue recibida y entregada a satisfacción. El CLIENTE acepta los términos de compra
                            generales publicados en www.idovela.con/TyC
                        </p>
                    </v-row>
                    <v-row>
                        <v-col cols="12" class="d-flex justify-space-between">
                            <div class="mb-2">
                                <CustomButton text="Volver" color="nero" @click="numberPag = 3" />
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
import TotalFlete from "../../components/global/TotalFlete.vue";
import TypePayment from "../../components/global/TypePayment.vue";
import Regalo from "../../components/icons/Regalo.vue";
import Promocion from "../../components/icons/Promocion.vue";
import Cubo from "../../components/icons/Cubo.vue";
import CustomFavorite from "../../components/icons/CustomFavorite.vue";
import Flecha from "../../components/icons/Flecha.vue";
import TotalPago from "../../components/global/TotalPago.vue";
import Encargado from "./Encargado.vue";

// const button = document.getElementById("customButton");
// const tooltip = document.getElementById("tooltip");
// const tooltipLink = document.getElementById("tooltipLink");

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
        Flecha,
        Encargado
    },
    data() {
        return {
            langSelectItems: ["COLOMBIA", "DEUTSCHLAND", "ENGLAND"],
            step: 1,
            numberPag: 1,
            darkBoxes: true,
            cartItems: [],
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
            useDefaultAddress1: false,
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
            mostrarFormEncargado: false
        };
    },
    computed: {
        ...mapGetters("auth", ["currentUser"]),
        ...mapState("cart", ["cartProducts", "cartPrice"]),
        ...mapGetters("cart", ["getCartPrice", "getCartCount"])
    },
    mounted() {
        this.$vuetify.theme.dark = false;
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
    methods: {
        ...mapActions("auth", ["getUser"]),
        showFormEncargado() {
            this.mostrarFormEncargado = !this.mostrarFormEncargado;
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
                    }
                });
                this.addressPrincipal = this.addressesParaEnvio[0];
                this.addressFacturacion = this.addressesParaFacturacion[0];
                this.addressServicio = this.addressesParaServicio[0];
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
                        this.numberPag = 4;
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
            console.log(evt);
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
    border: 1px solid #f5f5f5;
    background-color: #f5f5f5;
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
    border: 1px solid #f5f5f5;
    border-radius: 10px;
    padding: 10px;
    background: #f5f5f5;
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
    overflow-y: scroll;
    max-height: 450px;
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
    background: white;
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
    position: fixed;
    bottom: 0;
    width: 75%;
    background-color: transparent;
    backdrop-filter: blur(5px);
    margin-bottom: 0 !important;
}

.detalles-final {
    display: flex;
    justify-content: flex-end;
    align-items: center;
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
</style>
