<template>
    <div class="product-box-cart">
        <div class="product-box-cart-body">
            <v-row>
                    <v-col cols="3" md="1" class="d-flex justify-center justify-md-end align-center pr-0">
                        <custom-button
                                plain
                                :to="{ name: 'Suscriptions', params: { code: order } }"
                            >
                            <i class="las la-angle-left" style="font-size: 25px;"></i>
                            </custom-button>
                    </v-col>
                    <v-col cols="1" class="d-flex justify-center align-center pl-0">
                        <v-divider vertical/>
                    </v-col>
                    <v-col cols="12" md="5" class="align-center justify-center justify-md-start" style="display: grid;">
                        <div class="product-box-cart-info pl-0">
                            <p class="product-box-cart-brand-name mb-0">01 PLAN</p>
                            <h6 style="font-weight: bold;">NOMBRE DEL PLAN</h6>
                        </div>
                    </v-col>
                    <v-col cols="1" class="d-none d-md-flex justify-center align-center pl-0">
                        <v-divider vertical/>
                    </v-col>
                    <v-col cols="12" class="d-flex d-md-none justify-center align-center">
                        <v-divider />
                    </v-col>
                    <v-col cols="12" md="4">
                        <div class="product-box-cart-info pl-0 justify-center justify-md-start" style="display: grid;">
                            <p class="d-flex justify-center justify-md-start" style="font-weight: bold;">ADQUIRIDO</p>
                            <p class="mb-0">Fecha 02 de Julio, 1:30pm</p>
                        </div>
                    </v-col>
                </v-row>
        </div>
        <div class="d-none d-md-flex product-box-cart-actions">
            <div class="product-box-cart-actions-icons d-md-flex">
                <v-tooltip bottom color="black">
                    <template v-slot:activator="{ on, attrs }">
                        <div v-bind="attrs" v-on="on">
                            <custom-button
                                v-if="icon1"
                                plain
                                :to="{ name: 'OrderDetails', params: { code: order } }"
                            >
                                <Compartir />
                            </custom-button>
                        </div>
                    </template>
                    <span>Compartir</span>
                </v-tooltip>
                <v-tooltip bottom color="black">
                    <template v-slot:activator="{ on, attrs }">
                        <div v-bind="attrs" v-on="on">
                            <custom-button
                                v-if="icon3"
                                plain 
                                :to="{ name: 'OrderDetails', params: { code: order } }"
                            >
                            <Ojo />
                            </custom-button>
                        </div>
                    </template>
                    <span>Renovar</span>
                </v-tooltip>
            </div>
        </div>

        <div class="d-flex d-md-none justify-end align-center">
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                            v-bind="attrs"
                            v-on="on"
                            style="box-shadow: none; background-color: transparent;"
                            >
                            <Ellipsis />
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item class="pa-0">
                                <v-list-item-action class="mr-0">
                                    <custom-button
                                        v-if="icon1"
                                        plain
                                        :to="{ name: 'OrderDetails', params: { code: order } }"
                                    >
                                        <Compartir /><p class="mb-0 pl-1">Compartir</p>
                                    </custom-button>
                                    
                                </v-list-item-action>
                            </v-list-item>

                            <v-list-item class="pa-0">
                                <v-list-item-action class="mr-0">
                                    <custom-button
                                        v-if="icon3"
                                        plain 
                                        :to="{ name: 'OrderDetails', params: { code: order } }"
                                    >
                                    <Ojo /><p class="mb-0 pl-2">Favorito</p>
                                    </custom-button>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>


    </div>
</template>

<script>
import CustomButton from "./CustomButton.vue";
import Compartir from "../../components/icons/Compartir.vue";
import Ojo from "../../components/icons/Ojo.vue";

export default {
    components: {
        CustomButton,
        Compartir,
        Ojo
    },
    props: {
        order: String,
        date: String,
        descriptionStatus: String,
        colorStatus: String,
        icon1: String,
        icon2: String,
        icon3: String
    }
};
</script>

<style lang="scss" scoped>
.product-box-cart {
    display: grid;
    align-items: stretch;
    grid-template-columns: 10fr 2fr;

    border-radius: 10px;
    background-color: #f5f5f5;
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    box-sizing: border-box;
    padding: 0.5rem 0;

    @media (min-width: 768px) {
        grid-template-columns: 10fr 2fr;
        padding: 0.75rem 0;
    }

    &-body {
        display: flex;
    }

    &-body,
    &-body-price,
    &-quantity {
        border-right: 1px solid #dfdfdf;
    }

    &-body-price,
    &-quantity,
    &-actions {
        padding: 0 0.5rem;

        @media (min-width: 768px) {
            padding: 0 1.5rem;
        }
    }

    &-image {
        background-color: #dfdfdf;
        width: 90px;
        height: auto;
        aspect-ratio: 1;
        object-fit: cover;

        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;

        margin-top: -0.5rem;
        margin-bottom: -0.5rem;

        @media (min-width: 768px) {
            width: 110px;

            margin-top: -0.75rem;
            margin-bottom: -0.75rem;
        }

        img {
            // width: 100%;
            // height: auto;

            &.placeholder {
                width: 70%;
                margin: auto;
            }
        }
    }

    &-body-price {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    &-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0 0.5rem;

        @media (min-width: 768px) {
            padding: 0.75rem 1.25rem;
        }
    }

    &-reference {
        font-size: 10px;
        font-weight: 600;
        line-height: 24px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    &-name {
        font-size: var(--font-size-body1);
        font-weight: 700;
        line-height: calc(14px + (24 - 14) * var(--screen-size));
        letter-spacing: 0;
        text-transform: uppercase;
    }

    &-brand-name {
        font-family: "Roboto", sans-serif;
        font-size: var(--font-size-body1);
        line-height: 24px;
        letter-spacing: 0;
    }

    &-price {
        display: block;
        font-family: "Roboto", sans-serif;
        font-size: calc(12px + (18 - 12) * var(--screen-size));
        line-height: calc(14px + (24 - 14) * var(--screen-size));
        letter-spacing: 0;

        &:is(del) {
            font-size: calc(10px + (16 - 10) * var(--screen-size));
            opacity: 0.6;
        }

        &.discounted {
            background-color: #e8ff00;
            margin-bottom: 5px;
            line-height: 1;
        }
    }

    &-quantity {
        display: flex;
        align-items: center;
    }

    &-actions {
        display: flex;
        align-items: center;

        &-icons {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 1rem;
            justify-content: space-around;

            a,
            button {
                display: flex;
            }

            svg {
                opacity: 0.5;
                cursor: pointer;

                &:hover,
                &.active {
                    opacity: 1;
                }
            }
        }

        .v-menu {
            .v-list-item {
                min-height: 40px;
                padding: 0 12px;

                a,
                button {
                    display: flex;
                }

                svg {
                    opacity: 0.5;
                    width: 20px;

                    &.active {
                        opacity: 1;
                    }
                }
            }
        }
    }

    &::v-deep {
        .vue-numeric-input {
            width: 100% !important;
            min-width: 32px;

            @media (max-width: 767px) {
                height: 100%;
                min-height: 100px;

                .numeric-input {
                    height: 100%;
                    padding-top: 15px !important;
                }

                .btn-increment {
                    top: 0px;
                    bottom: unset;
                    min-height: 35px;
                    right: 8px;
                }

                .btn-decrement {
                    bottom: 12px;
                    left: 8px;
                    top: unset;
                }
            }

            @media (min-width: 768px) {
                .btn-increment {
                    right: 5px;
                }

                .btn-decrement {
                    left: 5px;
                }
            }

            .numeric-input {
                border: none;
                background: #ffffff;
                padding: 5px;
                border-radius: 5px;
            }

            .btn {
                background: none;
                box-shadow: none;
                border: none;
                cursor: pointer;
            }

            .btn-increment .btn-icon:before,
            .btn-decrement .btn-icon:before {
                width: 62.5%;
            }
        }
    }
}
.div-order {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 10px;
}

.div-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.color-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
}

.status {
    display: flex;
    gap: 8px;
}
</style>
