<template>
    <v-row>
        <v-col cols="12" md="7">
            <ProductGallery
                :is-loading="isLoading"
                :galleryImages="gallery"
                :galleryVideos="productDetails.videos"
                :dataSheet="productDetails.data_sheet"
            />
        </v-col>
        <v-col cols="12" md="5">
            <div v-if="!isLoading" class="add-to-cart-list-box pa-4 mb-3">
                <div class="d-flex justify-space-between">
                    <h5 class="subt2 font-weight-bold text-uppercase mb-2">
                        Referencia: {{ productDetails.reference }}
                    </h5>
                    <template v-if="isThisWishlisted(productDetails.id)">
                        <button
                            :title="$t('remove_from_wishlist')"
                            @click="removeFromWishlist(productDetails.id)"
                            class="icon"
                        >
                            <FavoriteIcon />
                        </button>
                    </template>
                    <template v-else>
                        <button :title="$t('add_to_wishlist')" @click="addNewWishlist(productDetails.id)" class="icon">
                            <FavoriteIcon />
                        </button>
                    </template>
                </div>
                <h1 class="h5 text-uppercase fw-600 mb-2">{{ productDetails.name }}</h1>
                <span class="body1 d-block mb-4">{{ productDetails.brand.name }}</span>
                <div class="d-flex mb-2">
                    <template v-if="productDetails.base_price > productDetails.base_discounted_price">
                        <h5 class="add-to-cart-price discounted">
                            {{ format_price(productDetails.base_discounted_price) }}
                        </h5>
                        <del class="add-to-cart-price">
                            {{ format_price(productDetails.base_price) }}
                        </del>
                    </template>
                    <h5 v-else class="add-to-cart-price">
                        {{ format_price(productDetails.base_discounted_price) }}
                    </h5>
                </div>
                <div class="d-flex justify-space-between align-center mb-3">
                    <span class="tx-caption">* Iva Incluido</span>
                    <vue-numeric-input
                        v-model="cartQuantity"
                        :min="1"
                        :max="maxCartLimit"
                        :step="1"
                        align="center"
                        size="132px"
                    />
                </div>
                <div class="add-to-cart-actions">
                    <Custom-Button
                        v-if="Number.isInteger(cartQuantity)"
                        @click="addCart()"
                        color="nero"
                        text="Agregar a Compras"
                    />
                    <CustomButton text="Consultar a un Asesor" color="grey" />
                </div>
            </div>

            <div class="add-to-cart-list-box pa-4 mb-3">
                <div class="add-to-cart-list-box-item">
                    <h5 class="subt2 fw-600 text-uppercase">Variación</h5>
                    <v-btn-toggle multiple borderless>
                        <v-btn class="blue" depressed :ripple="false" />
                        <v-btn class="grey" depressed :ripple="false" />
                        <v-btn class="black" depressed :ripple="false" />
                        <v-btn class="red" depressed :ripple="false" />
                    </v-btn-toggle>
                </div>
            </div>

            <ul class="add-to-cart-list-box pa-4 mb-3">
                <li class="add-to-cart-list-box-item mb-2">
                    <h5 class="subt2 text-uppercase">Disponibilidad</h5>
                    <span class="subt2">{{ productDetails?.stock ?? "000" }} en stock</span>
                </li>
                <li class="add-to-cart-list-box-item mb-2">
                    <h5 class="subt2 text-uppercase">Garantía</h5>
                    <span class="subt2">{{ productDetails?.warranty_text ?? "--" }}</span>
                </li>
                <li class="add-to-cart-list-box-item mb-2">
                    <h5 class="subt2 text-uppercase">Pago</h5>
                    <img src="/public/assets/img/product/icons-payments.png" alt="" />
                </li>
                <li class="add-to-cart-list-box-item">
                    <h5 class="subt2 text-uppercase">Envío</h5>
                    <span class="subt2">{{ productDetails?.shipping ?? "--" }}</span>
                </li>
            </ul>

            <!-- <div v-if="products.length > 0">
                <h5 class="subt2 fw-600 text-uppercase mb-2">Se incluye con la compra</h5>
                <v-divider class="mb-3" />
                <div class="add-to-cart-related-products overflow-y-auto overflow-uw">
                    <ProductBoxShort v-for="product in products" :key="product.id" :productDetails="product" />
                </div>
            </div> -->

            <v-divider class="d-sm-none mt-3" />
        </v-col>
    </v-row>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";
import ProductGallery from "../product/ProductGallery.vue";
import ProductBoxShort from "../product/ProductBoxShort.vue";
import CustomButton from "../../components/global/CustomButton.vue";

import FavoriteIcon from "../../components/icons/Favorite.vue";

export default {
    props: {
        isLoading: { type: Boolean, required: true, default: true },
        productDetails: { type: Object, required: true, default: {} },
        productType: { type: String, required: true, default: "product" }
    },
    data: () => ({
        cartQuantity: 1,
        chooseOptions: [],
        stock: 1,
        selectedVariation: {},
        minCartLimit: 1,
        maxCartLimit: Infinity
    }),
    watch: {
        productDetails: {
            immediate: true,
            handler(newVal, oldVal) {
                if (!this.is_empty_obj(newVal)) {
                    this.cartQuantity = 1;
                    this.stock = newVal.stock;
                    this.maxCartLimit = newVal.max_qty > 0 ? newVal.max_qty : Infinity;
                    this.minCartLimit = newVal.min_qty;
                    this.selectedVariation = newVal.is_variant == 1 ? {} : newVal.variations[0];
                    this.chooseOptions = [];
                }
            }
        }
    },
    components: {
        ProductGallery,
        ProductBoxShort,
        FavoriteIcon,
        CustomButton
    },
    computed: {
        ...mapGetters("wishlist", ["isThisWishlisted"]),
        ...mapGetters("cart", ["isThisInCart", "findCartItemByVariationId"]),
        discount() {
            return this.discount_percent(this.productDetails.base_price, this.productDetails.base_discounted_price);
        },
        gallery() {
            if (!this.is_empty_obj(this.productDetails)) {
                const thumbnailImage = {
                    src: this.productDetails.thumbnail_image,
                    type: "image"
                };

                return this.productDetails.photos.length ? this.productDetails.photos : [thumbnailImage];
            }

            return [];
        }
    },
    methods: {
        ...mapActions("wishlist", ["addNewWishlist", "removeFromWishlist"]),
        ...mapActions("cart", ["addToCart", "updateQuantity"]),
        ...mapActions("auth", ["showConversationDialog"]),
        ...mapMutations("auth", ["updateChatWindow"]),
        addCart() {
            if (this.productDetails.is_variant == 1) {
                // for variant product

                let chooseOptions = this.chooseOptions.filter(el => el != "");
                if (this.productDetails.variation_options.length > chooseOptions.length) {
                    // if all options is not selected

                    this.snack({
                        message: this.$i18n.t("please_select_all_options"),
                        color: "red"
                    });
                    return;
                }
            }

            if (!this.stock) {
                // selected variation stock check

                this.snack({
                    message: this.$i18n.t("this_product_is_out_of_stock"),
                    color: "red"
                });
                return;
            }

            let minMaxCheck = this.checkMinMaxLimit(this.selectedVariation.id);

            if (!minMaxCheck.success) {
                // selected variation min max limit check

                let message =
                    minMaxCheck.type == "min_limit"
                        ? `${this.$i18n.t("you_need_to_purchase_minimum_quantity")} ${this.minCartLimit}.`
                        : `${this.$i18n.t("you_can_purchase_maximum_quantity")} ${this.maxCartLimit}.`;

                this.snack({
                    message: message,
                    color: "red"
                });
                return;
            }

            this.addToCart({
                [`${this.productType}_id`]: this.productDetails[`${this.productType}_id`],
                variation_id: this.selectedVariation.id,
                qty: this.cartQuantity
            });

            this.snack({
                message: this.$i18n.t("product_added_to_cart"),
                color: "green"
            });
        },
        optionChosen() {
            let chooseOptions = this.chooseOptions.filter(el => el != "");
            if (this.productDetails.variation_options.length === chooseOptions.length) {
                let filteredVariations = this.productDetails.variations;

                chooseOptions.forEach(chosenOption => {
                    filteredVariations = filteredVariations.filter(variation => {
                        return variation.code.includes(chosenOption);
                    });
                });

                if (filteredVariations.length == 1) {
                    this.stock = filteredVariations[0].stock;
                    this.selectedVariation = filteredVariations[0];
                } else {
                    this.selectedVariation = {};
                }
            }
        },
        checkMinMaxLimit(variation_id) {
            if (this.isThisInCart(variation_id)) {
                // if this is already in cart check

                if (this.findCartItemByVariationId(variation_id).qty + this.cartQuantity < this.minCartLimit) {
                    // minimum purchase quantity check

                    return { success: false, type: "min_limit" };
                } else if (this.findCartItemByVariationId(variation_id).qty + this.cartQuantity > this.maxCartLimit) {
                    // maximum purchase quantity check

                    return { success: false, type: "max_limit" };
                }

                return { success: true, type: "" };
            } else {
                if (this.cartQuantity < this.minCartLimit) {
                    // minimum purchase quantity check

                    return { success: false, type: "min_limit" };
                } else if (this.cartQuantity > this.maxCartLimit) {
                    // maximum purchase quantity check

                    return { success: false, type: "max_limit" };
                }

                return { success: true, type: "" };
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.add-to-cart {
    &-list-box {
        border-radius: 10px;
        border: 1px solid #e4e4e4;

        &-item {
            display: flex;
            justify-content: space-between;
            align-items: center;

            h5 {
                font-weight: 600;
                line-height: 1;
            }
        }

        .v-btn-toggle {
            gap: 0.75rem;

            .v-btn {
                min-width: 28px;
                height: 28px !important;
                padding: 0;
                border-radius: 2px;

                &--active,
                &:hover:not(.v-btn--active) {
                    box-shadow: 0px 0px 0px 5px #dfdfdf;
                }
            }

            & > .v-btn:first-child,
            & > .v-btn:last-child {
                border-radius: 2px;
            }
        }
    }

    &-price {
        font-family: "Roboto", sans-serif;
        font-size: var(--font-size-h5);
        line-height: 24px;

        &.discounted {
            background-color: #e8ff00;
            margin-right: 0.5rem;
        }

        & + .add-to-cart-price {
            font-size: var(--font-size-body1);
        }
    }

    &-actions {
        display: grid;
        gap: 0.75rem;

        @media (min-width: 600px) and (max-width: 959px) {
            display: flex;
        }

        a {
            flex: 1;
        }
    }

    &-related-products {
        height: 37vh;
        overflow-x: hidden;
        padding-right: 8px;

        display: flex;
        flex-direction: column;
        gap: 5px;

        @media (min-width: 600px) {
            height: 22vh;
        }
    }
}
</style>
