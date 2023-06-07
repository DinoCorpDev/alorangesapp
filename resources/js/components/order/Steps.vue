<template>
    <v-stepper
        class="order-steps mb-4 mb-sm-0"
        v-if="!is_empty_obj(orderDetails)"
        elevation="0"
        :value="getCurrentStatus()"
        alt-labels
    >
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
                                    <span class="body2 text-uppercase font-weight-bold"> Entregado a cliente </span>
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
                                    <span class="body2 text-uppercase font-weight-bold"> Instalación concluida </span>
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
                <total :total="priceTotal" />
            </v-col>
        </v-row>
        <v-stepper-header>
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
        </v-stepper-header>
    </v-stepper>
</template>

<script>
import CustomButton from "../global/CustomButton.vue";

export default {
    props: {
        orderDetails: { type: Object, default: {} }
    },
    components: {
        CustomButton
    },
    data: () => ({}),
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
        }
    }
};
</script>
