<template>
    <div v-if="!is_empty_obj(orderDetails) && orderDetails.orders.length > 0">
        <v-sheet class="" color="white" elevation="0" v-for="(order, i) in orderDetails.orders" :key="i">
            <OrderPackage :order-details="order" />
        </v-sheet>
        <v-row>
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <div class="form">
                            <h6 class="black--text bold">Facturar a</h6>
                            <v-divider class="my-3" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Correo electronico</span>
                                <span class="body1">{{ orderDetails.user.email || "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Tipo de Persona</span>
                                <span class="body1">{{ "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Nombre</span>
                                <span class="body1">{{ orderDetails.user.name || "--" }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-3">
                                <span class="subtitle1 text-uppercase bold">Documento</span>
                                <span class="body1">
                                    {{ "--" }}
                                </span>
                            </div>
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
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                <span class="body1">Dirección principal</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                <span class="body1 text-right">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                <span class="body1">{{ orderDetails.shipping_address?.address }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                <span class="body1">{{ orderDetails.shipping_address?.postal_code }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                <span class="body1">{{ orderDetails.shipping_address?.country }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                <span class="body1">{{ orderDetails.shipping_address?.city }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                <span class="body1"> -- </span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold"> Nombre de quien lo va a recibir </span>
                                <span class="body1">{{ orderDetails.shipping_address?.name }}</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                <span class="body1">{{ orderDetails.shipping_address?.phone }}</span>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import OrderPackage from "./OrderPackage";

export default {
    components: {
        OrderPackage
    },
    computed: {
        ...mapGetters("app", ["appUrl"])
    },
    props: {
        orderDetails: { type: Object, default: () => {} }
    }
};
</script>

<style>
.container {
    background-color: #ffffff;
}

.form {
    border: 1px solid #e4e4e4;
    border-radius: 10px;
    padding: 10px;
}
</style>
