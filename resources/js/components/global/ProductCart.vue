<template>
    <div class="product-box-cart">
        <div class="product-box-cart-body">
            <img
                class="product-box-cart-image"
                :class="!productDetails.thumbnail_image ? 'placeholder' : ''"
                :src="productDetails.thumbnail_image || productPlaceholderUrl"
            />
            <div class="product-box-cart-info d-flex justify-center" v-if="productDetails && productDetails.name">
                <h2 class="product-box-cart-name">{{ productDetails.name || "--" }}</h2>
            </div>
        </div>
        <div class="product-box-cart-body-price">
            <template v-if="productCartType == 'checkout'">
                <!-- <span class="product-box-cart-price" :class="{ discounted: inDiscount }">
                    {{ formatearMoneda(productDetails.discounted_price * productDetails.qty) }}
                </span>
                <del v-if="inDiscount" class="product-box-cart-price">
                    {{ formatearMoneda(productDetails.regular_price * productDetails.qty) }}
                </del> -->

                {{
                    formatearMoneda(
                        productDetails.regular_price
                            ? productDetails.regular_price * productDetails.qty
                            : productDetails.base_price * cartQuantity
                    )
                }}
            </template>
            <template v-if="productCartType == 'bill'">
                {{
                    formatearMoneda(
                        productDetails.regular_price ? productDetails.regular_price : productDetails.base_price
                    )
                }}
            </template>

            <template v-if="productCartType == 'wishlist'">
                <span class="product-box-cart-price" :class="{ discounted: inDiscount }">
                    {{ formatearMoneda(productDetails.base_discounted_price) }}
                </span>
                <del
                    v-if="productDetails.base_price > productDetails.base_discounted_price"
                    class="product-box-cart-price"
                >
                    {{ formatearMoneda(productDetails.base_price) }}
                </del>
            </template>

            <template v-if="productCartType == 'purchase-history'">
                <span class="product-box-cart-price" :class="{ discounted: inDiscount }">
                    {{ formatearMoneda(productDetails.price) }}
                </span>
            </template>
        </div>
        <div class="product-box-cart-quantity">
            <vue-numeric-input
                v-model="cartQuantity"
                :min="1"
                :max="maxCartLimit"
                :step="1"
                :disabled="productCartType === 'bill'"
                align="center"
            />
        </div>
        <div class="product-box-cart-actions">
            <div class="product-box-cart-actions-icons d-none d-md-flex">
                <template v-if="productCartType == 'checkout' || productCartType == 'bill'">
                    <!-- <template v-if="productDetails.regular_price"> -->
                    <v-tooltip bottom color="black" v-if="productCartType != 'bill'">
                        <template v-slot:activator="{ on, attrs }">
                            <button @click="removeFromCart(productDetails.cart_id)" v-bind="attrs" v-on="on">
                                <TrashIcon />
                            </button>
                        </template>
                        <span>Eliminar</span>
                    </v-tooltip>

                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <button v-bind="attrs" v-on="on">
                                <router-link :to="{ name: 'ProductDetails', params: { slug: productDetails.slug } }">
                                    <EyeIcon />
                                </router-link>
                            </button>
                        </template>
                        <span>Ver detalles</span>
                    </v-tooltip>

                    <template v-if="isThisWishlisted(productDetails.product_id)">
                        <v-tooltip bottom color="black">
                            <template v-slot:activator="{ on, attrs }">
                                <button
                                    type="button"
                                    @click="removeFromWishlist(productDetails.product_id)"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <FavoriteIcon class="active" />
                                </button>
                            </template>
                            <span>Añadir a favoritos</span>
                        </v-tooltip>
                    </template>

                    <template v-else>
                        <v-tooltip bottom color="black">
                            <template v-slot:activator="{ on, attrs }">
                                <button
                                    type="button"
                                    @click="addNewWishlist(productDetails.product_id)"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <FavoriteIcon />
                                </button>
                            </template>
                            <span>Añadir a favoritos</span>
                        </v-tooltip>
                    </template>
                </template>

                <template v-if="productCartType == 'wishlist'">
                    <!-- <template v-if="productDetails.base_price"> -->
                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <button @click="removeFromWishlist(productDetails.id)" v-bind="attrs" v-on="on">
                                <TrashIcon />
                            </button>
                        </template>
                        <span>Eliminar de favoritos</span>
                    </v-tooltip>

                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <button v-bind="attrs" v-on="on">
                                <router-link :to="{ name: 'ProductDetails', params: { slug: productDetails.slug } }">
                                    <EyeIcon />
                                </router-link>
                            </button>
                        </template>
                        <span>Ver detalles</span>
                    </v-tooltip>

                    <v-tooltip bottom color="black">
                        <template v-slot:activator="{ on, attrs }">
                            <button @click="addCart()" v-bind="attrs" v-on="on">
                                <AddCartIcon />
                            </button>
                        </template>
                        <span>Agregar a carrito</span>
                    </v-tooltip>
                </template>
            </div>
            <div class="d-md-none">
                <v-menu attach bottom left>
                    <template v-slot:activator="{ on, attrs }">
                        <button class="d-flex" type="button" v-bind="attrs" v-on="on">
                            <EllipsisIcon />
                        </button>
                    </template>

                    <v-list>
                        <template v-if="productDetails.regular_price">
                            <v-list-item>
                                <button @click="removeFromCart(productDetails.cart_id)">
                                    <TrashIcon />
                                </button>
                            </v-list-item>
                            <v-list-item>
                                <router-link :to="{ name: 'ProductDetails', params: { slug: productDetails.slug } }">
                                    <EyeIcon />
                                </router-link>
                            </v-list-item>
                            <v-list-item>
                                <template v-if="isThisWishlisted(productDetails.product_id)">
                                    <button type="button" @click="removeFromWishlist(productDetails.product_id)">
                                        <FavoriteIcon class="active" />
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" @click="addNewWishlist(productDetails.product_id)">
                                        <FavoriteIcon />
                                    </button>
                                </template>
                            </v-list-item>
                        </template>

                        <template v-if="productDetails.base_price">
                            <v-list-item>
                                <button @click="removeFromWishlist(productDetails.id)">
                                    <TrashIcon />
                                </button>
                            </v-list-item>
                            <v-list-item>
                                <router-link :to="{ name: 'ProductDetails', params: { slug: productDetails.slug } }">
                                    <EyeIcon />
                                </router-link>
                            </v-list-item>
                            <v-list-item>
                                <button @click="addCart()">
                                    <AddCartIcon />
                                </button>
                            </v-list-item>
                        </template>
                    </v-list>
                </v-menu>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CustomButton from "../../components/global/CustomButton.vue";

import AddCartIcon from "../../components/icons/AddCartIcon.vue";
import EllipsisIcon from "../../components/icons/EllipsisIcon.vue";
import EyeIcon from "../../components/icons/EyeIcon.vue";
import FavoriteIcon from "../../components/icons/WishIcon.vue";
import TrashIcon from "../../components/icons/TrashIcon.vue";

export default {
    name: "ProductCart",
    components: {
        CustomButton,
        AddCartIcon,
        EllipsisIcon,
        EyeIcon,
        FavoriteIcon,
        TrashIcon
    },
    props: {
        productCartType: { type: String, default: "checkout" },
        productDetails: { type: Object, required: true, default: {} },
        actionLoading: false
    },
    data() {
        return {
            productPlaceholderUrl: "/public/assets/img/item-placeholder.png",
            cartQuantity: 1,
            minCartLimit: 1,
            maxCartLimit: Infinity
        };
    },
    computed: {
        ...mapGetters("wishlist", ["isThisWishlisted"]),
        thumbnailImage() {
            return this.productDetails.thumbnail || this.productDetails.thumbnail_image;
        },
        inDiscount() {
            if (this.productCartType == "checkout") {
                return this.productDetails.regular_price > this.productDetails.discounted_price;
            }

            if (this.productCartType == "wishlist") {
                return this.productDetails.base_price > this.productDetails.base_discounted_price;
            }
        }
    },
    watch: {
        productDetails: {
            immediate: true,
            handler(newVal, oldVal) {
                if (!this.is_empty_obj(newVal)) {
                    this.cartQuantity = newVal.qty || 1;
                    this.maxCartLimit = newVal.max_qty > 0 ? newVal.max_qty : Infinity;
                    this.minCartLimit = newVal.min_qty;
                }
            }
        },
        cartQuantity: {
            handler(newVal, oldVal) {
                // if new value is greater than max limit then set it to max limit then updateQuantity
                const { cart_id } = this.productDetails;

                if (Math.abs(newVal - oldVal) > 1) {
                    this.updateQuantity({ type: "set", cart_id: cart_id, qty: newVal });
                } else if (newVal > oldVal) {
                    this.updateQuantity({ type: "plus", cart_id: cart_id });
                } else if (newVal < oldVal && newVal > 0) {
                    this.updateQuantity({ type: "minus", cart_id: cart_id });
                }
            }
        }
    },
    methods: {
        ...mapActions("cart", ["updateQuantity", "toggleCartItem", "removeFromCart", "addToCart"]),
        ...mapActions("wishlist", ["addNewWishlist", "removeFromWishlist"]),
        addCart() {
            this.addToCart({
                product_id: this.productDetails,
                qty: this.cartQuantity
            })
                .then(() => {
                    this.snack({
                        message: this.$i18n.t("Producto agregado al carrito"),
                        color: "green"
                    });
                    // this.removeFromWishlist(this.productDetails.id);
                })
                .catch(error => {
                    console.error("Error al agregar al carrito:", error);
                    this.snack({
                        message: this.$i18n.t("Error agregando el producto"),
                        color: "red"
                    });
                });
        },
        formatearMoneda(valor) {
            return valor.toLocaleString("es-CO", {
                style: "currency",
                currency: "COP",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.theme--dark {
    .product-box-cart {
        &-body {
            color: #000000;
        }
    }
}

.product-box-cart {
    display: grid;
    align-items: stretch;
    grid-template-columns: 6fr 4fr 1fr 1fr;
    border: 1px solid #d2d7dd;
    border-radius: 10px;
    // background-color: #f5f5f5;
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    box-sizing: border-box;
    padding: 0.5rem 0;

    @media (min-width: 768px) {
        grid-template-columns: 5fr 2fr 2fr 3fr;
        padding: 0.75rem 0;
    }

    &-body {
        display: flex;
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
                    width: 30px;
                    height: 30px;
                }

                .btn-decrement {
                    left: 5px;
                    width: 30px;
                    height: 30px;
                }
            }

            .numeric-input {
                border: none;
                background: transparent;
                padding: 5px;
                border-radius: 5px;
            }

            .btn {
                background: #dddddd;
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
</style>
