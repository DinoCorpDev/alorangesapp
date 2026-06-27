<template>
    <div class="brand-item">
        <div class="brand-item-header">
            <template v-if="isThisWishlistedBrands(brandDetails.id)">
                <button type="button" class="icon active" @click="removeFromWishlistBrands(brandDetails.id)">
                    <FavoriteIcon />
                </button>
            </template>
            <template v-else>
                <button type="button" class="icon" @click="addNewWishlistBrands(brandDetails.id)">
                    <FavoriteIcon />
                </button>
            </template>
        </div>
        <div class="brand-item-body">
            <v-img :src="brandDetails.bgImg || '/public/assets/img/brand-item-bg-placeholder.png'" aspect-ratio="1" />
            <div class="brand-item-body-image">
                <v-img
                    :src="brandDetails.logo || 'public/assets/img/brand-item-img-placeholder.png'"
                    aspect-ratio="1"
                />
            </div>
        </div>
        <div class="brand-item-footer">
            <CustomButton
                block
                color="nero"
                text="Ver Detalles"
                :to="{ name: 'BrandDetails', params: { slug: brandDetails.slug } }"
            />
        </div>
    </div>
</template>

<script>
import CustomButton from "./CustomButton.vue";
import FavoriteIcon from "../icons/Favorite.vue";
import { mapActions, mapGetters, mapMutations } from "vuex";

export default {
    name: "CardBrand",
    components: {
        CustomButton,
        FavoriteIcon
    },
    computed: {
        ...mapGetters("wishlist", ["isThisWishlistedBrands"])
    },
    props: {
        brandDetails: {
            type: Object,
            default: () => {}
        }
    },
    mounted() {
        this.brandDetails.type = "brand";
    },
    methods: {
        ...mapActions("wishlist", ["addNewWishlistBrands", "removeFromWishlistBrands"]),
        ...mapActions("cart", ["addToCart", "updateQuantity"]),
        ...mapMutations("auth", ["showAddToCartDialog"]),
        addCart() {
            if (!this.$props.productDetails.is_variant) {
                this.addToCart({
                    variation_id: this.$props.productDetails.variations[0].id,
                    qty: this.$props.productDetails.min_qty
                });
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.brand-item {
    border-radius: 10px;
    background-color: #f5f5f5;
    height: 100%;
    transition: all 0.3s ease-in-out;

    &:hover {
        background-color: transparent;
    }

    &-header {
        background-color: #f5f5f5;
        display: flex;
        justify-content: flex-end;
        padding: 0.75rem 1rem;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;

        &::v-deep {
            .icon {
                line-height: 0.5;

                path {
                    fill: #040405;
                    opacity: 0.5;
                }

                &:hover {
                    path {
                        opacity: 0.8;
                    }
                }

                &:active {
                    path {
                        opacity: 1;
                    }
                }
            }
        }
    }

    &-body {
        &-image {
            width: 85%;
            background-color: #ffffff;
            margin: auto;
            margin-top: -40%;
            position: relative;
            border-radius: 50%;
            margin-bottom: 2rem;
        }
    }

    &-footer {
        padding: 0.75rem 1rem;
    }
}
</style>
