<template>
    <div v-if="!is_empty_obj(orderDetails) && orderDetails.orders.length > 0">
        <v-sheet class="" color="white" elevation="0" v-for="(order, i) in orderDetails.orders" :key="i">
            <!-- <OrderPackage :order-details="order" /> -->
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
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <h5 class="fw-600">Facturar a nombre de</h5>
                        <v-divider class="my-4" />
                        <div class="form">
                            <h6 class="black--text bold">Usuario principal</h6>
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
                        <div class="form" v-if="payment_method.payment_method">
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">{{payment_method.payment_method.extra.brand || 'PSE'}}</span>
                                <span class="body1 pr-3">{{ payment_method.payment_method.extra.last_four ? `************${payment_method.payment_method.extra.last_four}` : payment_method.status }}</span>
                            </div>
                        </div>
                        <div class="form" v-else-if="contraentregaType !== false">
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold pl-3">Contraentrega</span>
                                <span class="body1 pr-3">Pago con {{contraentregaType}}</span>
                            </div>
                        </div>
                        <div class="form" v-else>
                            <div>Transferencia Bancolombia</div>
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
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import Regalo from "../../components/icons/Regalo.vue";
import OrderPackage from "./OrderPackage.vue";

export default {
    data(){
        return {
            payment_method:{},
            contraentregaType: false,
        }
    },
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
            this.payment_method = this.orderDetails.orders[0].manual_payment;
            if (this.orderDetails.orders[0].metodo_pago_contraentrega) {
                this.contraentregaType = this.orderDetails.orders[0].metodo_pago_contraentrega
            }    
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

.bold {
    font-weight: bold;
}
</style>
