<template>
    <div v-if="!is_empty_obj(orderDetails) && orderDetails.orders.length > 0">
        <v-sheet class="" color="white" elevation="0" v-for="(order, i) in orderDetails.orders" :key="i">
            <OrderPackage :order-details="order" />
        </v-sheet>
        <v-row>
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <h5 class="fw-600">Dirección de envio</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <h6 class="black--text bold">Nombre de direccion</h6>
                            <v-divider class="my-3" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Nombre de Dirección</span>
                                <span class="body1 pr-3">Dirección principal</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Dirección</span>
                                <span class="body1 text-right pr-3">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3"> Descripción de Dirección </span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Codigo Postal</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.postal_code }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Departamento</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.country }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Municipio</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.city }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Barrio</span>
                                <span class="body1 pr-3"> -- </span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Telefono / Celular</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.phone }}</span>
                            </div>
                        </div>

                        <div class="form">
                            <h6 class="black--text bold">Encargado</h6>
                            <v-divider class="my-3" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Nombre</span>
                                <span class="body1 pr-3">{{ orderDetails.user.name || "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-3">
                                <span class="subtitle1 text-uppercase bold pl-3">TELÉFONO / CELULAR</span>
                                <span class="body1 pr-3">
                                    {{ "--" }}
                                </span>
                            </div>
                        </div>

                        <h5 class="fw-600">Dirección de servicio</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <h6 class="black--text bold">Nombre de direccion servicio</h6>
                            <v-divider class="my-3" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Nombre de Dirección</span>
                                <span class="body1 pr-3">Dirección principal</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Dirección</span>
                                <span class="body1 text-right pr-3">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3"> Descripción de Dirección </span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Codigo Postal</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.postal_code }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Departamento</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.country }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Municipio</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.city }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Barrio</span>
                                <span class="body1 pr-3"> -- </span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Telefono / Celular</span>
                                <span class="body1 pr-3">{{ orderDetails.shipping_address?.phone }}</span>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <h5 class="fw-600">Facturar a nombre de</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <h6 class="black--text bold">Uuario principal</h6>
                            <v-divider class="my-3" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Correo electronico</span>
                                <span class="body1 pr-3">{{ orderDetails.user.email || "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Tipo de Persona</span>
                                <span class="body1 pr-3">{{ "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Nombre</span>
                                <span class="body1 pr-3">{{ orderDetails.user.name || "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-3">
                                <span class="subtitle1 text-uppercase bold pl-3">Documento</span>
                                <span class="body1 pr-3">
                                    {{ "--" }}
                                </span>
                            </div>
                        </div>

                        <h5 class="fw-600">Medio de pago</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <h6 class="black--text bold">Método de pago</h6>
                            <v-divider class="my-3" />
                            <div>acá va la info del metodo de pago</div>
                        </div>
                        <h5 class="fw-600">Código promocional</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <div class="d-flex"><regalo /></div>
                            <v-divider class="my-3" />
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
                Protección de datos personales: IDOVELA S.A.S garantiza el tratamiento de datos personales acorde a lo
                establecido en la ley 1581/2012 y decreto 1377/2013. por favor dirija sus inquietudes al correo:
                soporte@idovela.com Riesgo de lavado de activos, financiación al terrorismo sarlaft y ley antisoborno:
                EL CLIENTE certifica a IDOVELA S.A.S que sus recursos no provienen ni se destinan al ejercicio de
                ninguna actividad ilícita o de actividades conexas al lavado de activos provenientes de estas o de
                actividades relacionadas con la financiación del terrorismo EL CLIENTE se obliga a realizar todas las
                actividades encaminadas a asegurar que los recursos de estos, no se encuentran relacionados, provengan,
                de actividades ilícitas, particularmente de lavado de activos o financiación del terrorismo. Igualmente
                se compromete a respetar el programa de ética empresarial que aplica en IDOVELA S.A.S. Garantía: 1).
                Garantiza el articulo a partir de la fecha prescrita. únicamente garantía de fábrica. 2). Garantizamos
                el articulo según nos ofrecen nuestro(s) proveedor(es). 3). Situaciones no cubiertas por la garantía
                legal: • Productos cuyo periodo de garantía haya expirado o finalizado. • Situaciones ocasionadas por
                fuerza mayor, caso fortuito, culpa exclusiva del usuario o terceros • Problemas causados como
                consecuencia de instalaciones físicas y eléctricas inadecuadas, uso o mantenimiento inadecuados o
                diferentes al indicado en el manual del producto, por personal no autorizado por iDOVELA S.A.S. Cambios
                y devoluciones: después de 30 días fecha factura, para cambios por favor presentar la factura original
                en horario de lunes a sábado de 8:00 am a 5:00 pm. la mercancía se recibe en perfecto estado, caja
                sellada, el material en calidad de segunda (uso o deterioro) no tiene cambios. no devolvemos dinero
                Logística: la mercancía se deja hasta dónde llega el camión (primera planta). una vez recibida y firmada
                esta factura se entenderá que la mercancía fue recibida y entregada a satisfacción. El CLIENTE acepta
                los términos de compra generales publicados en www.idovela.con/TyC
            </p>
        </v-row>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import Regalo from "../../components/icons/Regalo.vue";
import OrderPackage from "./OrderPackage.vue";

export default {
    components: {
        OrderPackage,
        Regalo
    },
    computed: {
        ...mapGetters("app", ["appUrl"])
    },
    props: {
        orderDetails: { type: Object, default: () => {} }
    },
    created() {
        this.iterateOrderUpdates();
    },
    methods: {
        iterateOrderUpdates() {
            const orderUpdates = this.orderDetails.order_updates;
            this.orderDetails.orders[0].order_updates = orderUpdates;
        }
    }
};
</script>

<style>
.container {
    background-color: #fafcfc;
}

.form {
    border: 1px solid #f5f5f5;
    border-radius: 10px;
    padding: 10px;
    background: #f5f5f5;
    margin-bottom: 15px;
}

.bold{
    font-weight: bold;
}
</style>
