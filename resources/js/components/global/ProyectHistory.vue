<template>
    <div class="product-box-cart">
        <div class="product-box-cart-body">
            <div class="product-box-cart-info">
                <p class="product-box-cart-brand-name mb-0">CDD0097{{ order }}</p>
                <h6 class="product-box-cart-name">NOMBRE DE LA COLECCION</h6>
            </div>     
        </div>
        <div class="product-box-cart-actions">
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
                                v-if="icon2"
                                plain
                                :to="{ name: 'ProyectDetails', params: { code: order } }"
                            >
                            <Ojo />
                            </custom-button>
                        </div>
                    </template>
                    <span>Ver detalles</span>
                </v-tooltip>
                <v-tooltip bottom color="black">
                    <template v-slot:activator="{ on, attrs }">
                        <div v-bind="attrs" v-on="on">
                            <custom-button
                                v-if="icon3"
                                plain 
                                :to="{ name: 'OrderDetails', params: { code: order } }"
                            >
                            <Favorito />
                            </custom-button>
                        </div>
                    </template>
                    <span>Añadir a favoritos</span>
                </v-tooltip>
            </div>
        </div>
    </div>
</template>

<script>
import CustomButton from "./CustomButton.vue";
import Favorito from "../../components/icons/Favorito.vue";
import Compartir from "../../components/icons/Compartir.vue";
import Ojo from "../../components/icons/Ojo.vue";

export default {
    components: {
        CustomButton,
        Favorito,
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
