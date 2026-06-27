<template>
    <div class="product-box">
        <div class="product-box-image">
            <v-img
                :src="productDetails.thumbnail_image || productPlaceholderUrl"
                :alt="productDetails.name"
                :aspect-ratio="1"
            />
            <div class="product-box-image-hover">
                <CustomButton
                    block
                    color="orange"
                    text="Ver Detalles"
                    :to="{ name: 'ProductDetails', params: { slug: productDetails.slug } }"
                />.
            </div>
        </div>
        <div class="product-box-body">
            <p class="product-box-reference mb-3" v-if="productDetails.reference">
                {{ productDetails.reference || "--" }}
            </p>
            <h2 class="product-box-name mb-1">{{ productDetails.name || "--" }}</h2>
            <p class="product-box-brand-name mb-3" v-if="productDetails.brandName">
                {{ productDetails.brandName || "--" }}
            </p>
            <template v-if="productDetails.base_price > productDetails.base_discounted_price">
                <del class="product-box-price discounted">{{ formatearMoneda(productDetails.base_price) }}</del>
            </template>
            <span class="product-box-price">{{ formatearMoneda(productDetails.base_discounted_price) }}</span>
            <template v-if="boxStyle == 'two'">
                <!-- <v-divider class="my-4" /> -->
                <p class="product-box-description" v-if="productDetails.description">
                    {{ productDetails.description || "--" }}
                </p>
            </template>
        </div>
        <div class="product-box-footer pt-0 d-flex">
            <CustomButton color="orange" @click="addCart()" :loading="actionLoading" :disabled="actionLoading">
                Añadir <Cart class="ml-1" />
            </CustomButton>
            <template v-if="isThisWishlisted(productDetails.id)">
                <v-tooltip bottom color="black">
                    <template v-slot:activator="{ on, attrs }">
                        <button
                            type="button"
                            class="icon active"
                            @click="removeFromWishlist(productDetails.id)"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <WishIcon />
                        </button>
                    </template>
                    <span>Quitar de favoritos</span>
                </v-tooltip>
            </template>
            <template v-else>
                <v-tooltip bottom color="black">
                    <template v-slot:activator="{ on, attrs }">
                        <button
                            type="button"
                            class="icon"
                            @click="addNewWishlist(productDetails.id)"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <WishIcon />
                        </button>
                    </template>
                    <span>Añadir de favoritos</span>
                </v-tooltip>
            </template>
        </div>
    </div>
</template>

<script>
import { mapActions, mapMutations, mapGetters } from "vuex";

import CustomButton from "../global/CustomButton.vue";
import FavoriteIcon from "../icons/Favorite.vue";
import Cart from "../icons/CartIconSmall.vue";
import WishIcon from "../icons/WishIcon.vue";

export default {
    name: "ProductBox",
    components: {
        CustomButton,
        WishIcon,
        Cart,
        FavoriteIcon
    },
    props: {
        boxStyle: { type: String, default: "one" },
        productDetails: { type: Object, required: true, default: {} },
        actionLoading: false
    },
    mounted() {
        this.productDetails.type = "product";
    },
    data() {
        return {
            isAddingToCart: false,
            productPlaceholderUrl: "/public/assets/img/item-placeholder.png"
        };
    },
    computed: {
        ...mapGetters("wishlist", ["isThisWishlisted"])
    },
    methods: {
        ...mapActions("wishlist", ["addNewWishlist", "removeFromWishlist"]),
        ...mapActions("cart", ["addToCart", "updateQuantity"]),
        ...mapMutations("auth", ["showAddToCartDialog"]),
        addCart() {
            if (!this.isAddingToCart && !this.productDetails.is_variant) {
                this.isAddingToCart = true; // Marcar que está en proceso

                this.addToCart({
                    product_id: this.productDetails,
                    qty: 1
                })
                    .then(() => {
                        this.snack({
                            message: this.$i18n.t("Producto agregado al carrito"),
                            color: "green"
                        });
                    })
                    .catch(error => {
                        console.error("Error al agregar al carrito:", error);
                        this.snack({
                            message: this.$i18n.t("Error agregando el producto"),
                            color: "red"
                        });
                    })
                    .finally(() => {
                        this.isAddingToCart = false;
                    });
            }
        },
        formatearMoneda(valor) {
            return valor.toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.theme--dark {
    .product-box {
        &-body {
            color: #000;
        }
    }
}

.product-box {
    border: 1px solid #e4e4e4;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2) !important;
    display: flex;
    flex-direction: column;
    border-radius: 10px;
    background-color: transparent;
    height: 100%;
    overflow: hidden;
    box-sizing: border-box;

    &-body,
    &-image-hover,
    &-footer {
        padding: 0.5rem;

        @media (min-width: 600px) {
            padding: 0.65rem 0.7rem;
        }
        &::v-deep {
            .icon {
                line-height: 0.5;

                @media (max-width: 600px) {
                    svg {
                        height: 32px;
                        width: 32px;
                    }
                }

                path {
                    fill: #040405;
                    opacity: 0.5;
                }

                &:hover {
                    path {
                        opacity: 0.8;
                    }
                }

                &.active {
                    path {
                        fill: #f38637;
                        opacity: 1;
                    }
                }
            }
        }
    }

    &-image {
        position: relative;
        cursor: pointer;
        padding: 10px 10px 0px 10px;
        background: transparent;
        &::after {
            content: "";
            display: block;
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        &-hover {
            width: 90%;
            position: absolute;
            bottom: 0;
            opacity: 0;
        }

        &:hover {
            &::after {
                opacity: 1;
            }

            .product-box-image-hover {
                opacity: 1;
            }
        }

        button,
        a {
            position: relative;
            z-index: 2;
        }
    }

    &-body {
        flex: 1;

        .v-divider {
            background-color: #e4e4e4;
        }
    }

    &-footer {
        // .v-btn {
        //     @media (max-width: 600px) {
        //         font-size: 11px;
        //         height: 30px !important;
        //     }
        // }
        display: flex;
        justify-content: space-between
    }

    &-reference {
        font-size: var(--font-size-caption2);
        font-weight: 600;
        line-height: 13px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    &-name {
        font-size: var(--font-size-body1);
        font-weight: 700;
        line-height: 24px;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    &-brand-name {
        font-family: "Roboto", sans-serif;
        font-size: 15px;
        line-height: 24px;
        letter-spacing: 0;
    }

    &-price {
        display: block;
        font-family: "Roboto", sans-serif;
        font-size: 18px;
        line-height: 24px;
        letter-spacing: 0;

        &.discounted {
            font-size: var(--font-size-body1);

            & + .product-box-price {
                background-color: #e8ff00;
                display: inline-block;
            }
        }
    }

    &-description {
        font-family: "Roboto", sans-serif;
        font-size: 15px;
        letter-spacing: 0.5px;
        line-height: 18px;
    }
}
</style>
