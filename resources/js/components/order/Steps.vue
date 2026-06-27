<template>
    <v-stepper
        class="order-steps mb-4 mb-sm-0"
        v-if="!is_empty_obj(orderDetails)"
        elevation="0"
        :value="getCurrentStatus()"
        alt-labels
    >
        <v-row style="background-color: #fafcfc;">
            <v-col cols="12" sm="6">
                <!-- <div class="form">
                    <h6 class="black--text bold">Seguimiento de envió</h6>
                    <v-divider class="my-3" />
                    <v-timeline dense>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold"> En verificación </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getSendingStatus("verificacion"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">Facturación</span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getSendingStatus("facturacion"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">Alistamiento</span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getSendingStatus("alistamiento"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">
                                        Recogido por transportadora
                                    </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getSendingStatus("transportadora"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold"> Entregado a cliente </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getSendingStatus("entregado"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                    </v-timeline>
                    <a href="#" class="black--text text-decoration-underline font-weight-bold">
                        Conocer la logistica de envió
                    </a>
                </div> -->
            </v-col>
            <v-col cols="12" sm="6">
                <div class="form">
                    <h6 class="black--text bold">Seguimiento de servicio</h6>
                    <v-divider class="my-3" />
                    <v-timeline dense>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">Comprado</span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getServiceStatus("comprado"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">Agendamiento</span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getServiceStatus("agendamiento"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold"> En preparación </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getServiceStatus("preparacion"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold">
                                        Verificación de adecuación
                                    </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getServiceStatus("verificacion"))
                                    }}</span>
                                </v-col>
                            </v-row>
                        </v-timeline-item>
                        <v-timeline-item color="black" small fill-dot>
                            <v-row>
                                <v-col class="d-flex justify-start align-center">
                                    <span class="body2 text-uppercase font-weight-bold"> Instalación concluida </span>
                                </v-col>
                                <v-col class="d-flex justify-end align-center">
                                    <span class="body2 font-weight-bold">{{
                                        format_date(getServiceStatus("concluida"))
                                    }}</span>
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
        <!-- <v-stepper-header>
            <template v-for="(step, i) in steps">
                <v-stepper-step
                    :key="`${i}-step`"
                    :step="i + 1"
                    :complete="getStepStatus(step.status)"
                    complete-icon="la-check"
                    error-icon="la-times"
                    :rules="[getCancelStatus]"
                    color="green"
                >
                    {{ getLevel(step.level) }}
                </v-stepper-step>
                <v-divider
                    v-if="i !== steps.length - 1"
                    :key="i"
                    :class="[{ complete: getStepStatus(step.status) }]"
                ></v-divider>
            </template>
        </v-stepper-header> -->
    </v-stepper>
</template>

<script>
import CustomButton from "../global/CustomButton.vue";
import moment from "moment";
import "moment/locale/es";
moment.locale("es");

Vue.filter("formatDate", function (value) {
    if (value) {
        return moment(String(value)).format("MM/DD/YYYY hh:mm");
    }
});

export default {
    props: {
        orderDetails: { type: Object, default: {} },
        actualizacionesEnvio: [],
        actualizacionesServicio: []
    },
    components: {
        CustomButton
    },
    data() {
        return {
            serviceUpdates: [],
            sendingUpdates: []
        };
    },
    computed: {
        steps() {
            return [
                {
                    level: this.$i18n.t("order_placed"),
                    status: "order_placed"
                },
                {
                    level: this.$i18n.t("confirmed"),
                    status: "confirmed"
                },
                {
                    level: this.$i18n.t("processed"),
                    status: "processed"
                },
                {
                    level: this.$i18n.t("shipped"),
                    status: "shipped"
                },
                {
                    level: this.$i18n.t("delivered"),
                    status: "delivered"
                }
            ];
        }
    },
    created() {
        this.getOrderUpdates();
    },
    methods: {
        getLevel(level) {
            return this.orderDetails.delivery_status == "cancelled" ? this.$i18n.t("cancelled") : level;
        },
        getCancelStatus() {
            return this.orderDetails.delivery_status == "cancelled" ? false : true;
        },
        getCurrentStatus() {
            return this.steps.findIndex(step => step.status == this.orderDetails.delivery_status) + 2;
        },
        getStepStatus(status) {
            let activeIndex = this.steps.findIndex(step => step.status == this.orderDetails.delivery_status);
            let currentIndex = this.steps.findIndex(step => step.status == status);
            return currentIndex <= activeIndex;
        },
        getOrderUpdates() {
            this.orderDetails.order_updates.forEach((val, i) => {
                if (val.status_id === 0) {
                    // Actualizacion tipo envio
                    this.sendingUpdates.push(val);
                }
                if (val.status_id === 1) {
                    // Actualizacion tipo servicio
                    this.serviceUpdates.push(val);
                }
            });
        },
        getSendingStatus(key) {
            let elemToFind = "";
            this.sendingUpdates.forEach(val => {
                if (val.status_key === key) elemToFind = val.created_at;
            });
            return elemToFind;
        },
        getServiceStatus(key) {
            let elemToFind = "";
            this.serviceUpdates.forEach(val => {
                if (val.status_key === key) elemToFind = val.created_at;
            });
            return elemToFind;
        },
        format_date(value) {
            if (value) {
                return moment(String(value)).format("DD. MMMM YYYY");
            }
        }
    }
};
</script>
