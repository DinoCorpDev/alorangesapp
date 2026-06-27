<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <order
                    style="background-color: #f5f5f5"
                    order="ID1222"
                    day="07"
                    month="Julio"
                    year="2020"
                    colorStatus="red"
                    descriptionStatus="Por aprobar pedido"
                    icon1="la-download"
                    icon2="la-eye"
                    icon3="la-print"
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
                            <select-custom dark label="Usuario Principal" :items="langSelectItems" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Correo electronico</span>
                                <span class="body1">user@gmail.com</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Tipo de Persona</span>
                                <span class="body1">Natural</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Nombre</span>
                                <span class="body1">Alejando Ramirez</span>
                            </div>
                            <div class="d-flex justify-space-between mb-3">
                                <span class="subtitle1 text-uppercase bold">Documento</span>
                                <span class="body1">C.C. 1010101011</span>
                            </div>
                            <custom-button class="mr-3" text="Editar" />
                            <custom-button text="Añadir" />
                        </div>
                    </v-col>
                    <v-col cols="12" md="6" class="details">
                        <v-row>
                            <v-col cols="12">
                                <div class="form">
                                    <h6 class="black--text bold">Dirección de envio</h6>
                                    <v-divider class="my-3" />
                                    <select-custom dark label="Usuario Principal" :items="langSelectItems" />
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                        <span class="body1">Casa 1</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Dirección</span>
                                        <span class="body1">Avenida siempre viva 123</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                        <span class="body1">Casa de tejado con color café y patio verde</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                        <span class="body1">110611</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Departamento</span>
                                        <span class="body1">Cundinamarca</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Municipio</span>
                                        <span class="body1">Bogota d.c</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Barrio</span>
                                        <span class="body1"> -- </span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">
                                            Nombre de quien lo va a recibir
                                        </span>
                                        <span class="body1">Alejandro Ramirez</span>
                                    </div>
                                    <div class="d-flex justify-space-between mb-2">
                                        <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                        <span class="body1">+57 300 544 3300</span>
                                    </div>
                                    <custom-button class="mr-3" text="Editar" />
                                    <custom-button text="Añadir" />
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <div class="form">
                            <h6 class="black--text bold">Dirección de servicio</h6>
                            <v-divider class="my-3" />
                            <label class="label">
                                <input type="checkbox" />
                                <span class="body-1 black--text text">
                                    Usar la misma Dirección de envió para que Aloranges preste los servicio de
                                    instalación, mantenimiento y más.
                                </span>
                                <span class="checkmark"></span>
                            </label>
                            <v-divider class="my-3" />
                            <custom-button block text="Añadir Dirección" />
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <div class="form">
                            <h6 class="black--text bold">Dirección de facturacion</h6>
                            <v-divider class="my-3" />
                            <label class="label">
                                <input type="checkbox" />
                                <span class="body-1 black--text text">
                                    Usar la misma Dirección de envió para que Idovela entreguela factura física.
                                </span>
                                <span class="checkmark"></span>
                            </label>
                            <v-divider class="my-3" />
                            <custom-button block text="Añadir Dirección" />
                        </div>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" md="6" class="details2">
                <v-row>
                    <v-col cols="12">
                        <div class="form">
                            <h6 class="black--text bold">Dirección de envio</h6>
                            <v-divider class="my-3" />
                            <select-custom dark label="Usuario Principal" :items="langSelectItems" />
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Nombre de Dirección</span>
                                <span class="body1">Casa 1</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Dirección</span>
                                <span class="body1">Avenida siempre viva 123</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold"> Descripción de Dirección </span>
                                <span class="body1">Casa de tejado con color café y patio verde</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Codigo Postal</span>
                                <span class="body1">110611</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Departamento</span>
                                <span class="body1">Cundinamarca</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Municipio</span>
                                <span class="body1">Bogota d.c</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Barrio</span>
                                <span class="body1"> -- </span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold"> Nombre de quien lo va a recibir </span>
                                <span class="body1">Alejandro Ramirez</span>
                            </div>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="subtitle1 text-uppercase bold">Telefono / Movil</span>
                                <span class="body1">+57 300 544 3300</span>
                            </div>
                            <custom-button class="mr-3" text="Editar" />
                            <custom-button text="Añadir" />
                        </div>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <div class="my-5">
            <h6>Lista de Pedido</h6>
            <v-divider class="my-3" />
            <v-row>
                <v-col cols="12">
                    <product-cart
                        price="000.000.000 COP"
                        quantity="3"
                        icon1="/public/assets/img/icons/back.svg"
                        icon3="/public/assets/img/icons/marker.svg"
                    />
                </v-col>
                <v-col cols="12">
                    <product-cart
                        price="000.000.000 COP"
                        quantity="3"
                        icon1="/public/assets/img/icons/back.svg"
                        icon3="/public/assets/img/icons/marker.svg"
                    />
                </v-col>
                <v-col cols="12">
                    <product-cart
                        price="000.000.000 COP"
                        quantity="3"
                        icon1="/public/assets/img/icons/back.svg"
                        icon3="/public/assets/img/icons/marker.svg"
                    />
                </v-col>
            </v-row>
            <v-divider class="my-3" />
            <div class="div-total">
                <div>
                    <p class="subtitle-2 text-uppercase font-weight-bold">Sub-total</p>
                    <p class="body-2">000.000.000 COP</p>
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
                    <p class="body-1">000.000.000 COP</p>
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
        </v-row>
    </v-container>
</template>

<script>
import CustomButton from "../../components/global/CustomButton.vue";
import CustomInput from "../../components/global/CustomInput.vue";
import Order from "../../components/global/Order.vue";
import ProductCart from "../../components/global/ProductCart.vue";
import SelectCustom from "../../components/global/SelectCustom.vue";
import TypePayment from "../../components/global/TypePayment.vue";

export default {
    components: {
        CustomButton,
        CustomInput,
        ProductCart,
        SelectCustom,
        Order,
        TypePayment
    },
    data() {
        return {
            langSelectItems: ["COLOMBIA", "DEUTSCHLAND", "ENGLAND"]
        };
    }
};
</script>

<style lang="scss" scoped>
::v-deep {
    .v-stepper__header {
        box-shadow: none;
    }

    .v-stepper__content {
        padding: 24px 0px;
    }

    .v-stepper__step--editable {
        background-color: #dfdfdf;
        height: 50%;
        box-shadow: none;
        border-radius: 10px;
    }

    .v-stepper__step .v-stepper__label {
        color: #000000 !important;
    }

    .v-stepper__step .v-stepper__label:hover {
        color: #ffffff !important;
    }

    .theme--light.v-stepper .v-stepper__step--editable:hover {
        background-color: #000000;
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

.container {
    background-color: #ffffff;
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

.details {
    display: none;
}
.details2 {
    display: flex;
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
@media (max-width: 1020px) {
    .details {
        display: flex;
        flex-wrap: wrap;
    }
    .details2 {
        display: none;
    }
}

.success {
    background-color: #06c167;
    color: #ffffff;
    border-radius: 50%;
    padding: 5px 5px 0px 5px;
}
</style>
