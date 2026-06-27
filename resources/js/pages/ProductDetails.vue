<template>
    <v-container fluid class="product-details">
        <v-row tag="main" class="mb-6 pa-1 pa-sm-5">
            <v-col cols="12" lg="12">
                <AddToCart :is-loading="detailsLoading" :product-details="productDetails" :products="moreProducts" />
            </v-col>
            <v-col cols="12" class="">
                <ProductsGrid class="mb-6" :products="moreProducts" filtrable cols="6" sm="4" md="3" lg="2" />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import AddToCart from "../components/product/AddToCart.vue";
import CustomButton from "../components/global/CustomButton.vue";
import ProductsGrid from "../components/global/ProductsGrid.vue";

import CubesIcon from "../components/icons/Cubes.vue";
import MapPinIcon from "../components/icons/MapPin.vue";
import SyncIcon from "../components/icons/Sync.vue";
import ToolsIcon from "../components/icons/Tools.vue";

export default {
    metaInfo() {
        return {
            title: this.metaTitle
        };
    },
    data: () => ({
        metaTitle: "",
        detailsLoading: true,
        productDetails: {},
        ratings: { rating5: 67, rating4: 22, rating3: 11, rating2: 0, rating1: 0, vidaUtil: 0 },
        desserts: [],
        impacto: "28%",
        moreProducts: [],
        footerProducts: [],
        tab: null,
        relatedProducts: [{}, {}, {}, {}, {}],
        rating: 0
    }),
    components: {
        AddToCart,
        CustomButton,
        ProductsGrid,
        CubesIcon,
        MapPinIcon,
        SyncIcon,
        ToolsIcon
    },
    methods: {
        async getDetails() {
            const res = await this.call_api("get", `product/details/${this.$route.params.slug}`);

            if (res.data.success) {
                this.productDetails = res.data.data;
                this.setVidaUtil(this.productDetails.vida_util);
                this.setComposition(this.productDetails);
                this.setImpacto(this.productDetails?.impacto_ambiental);
                this.getMoreProducts(this.productDetails?.category_name);
                this.getMoreProducts2(this.productDetails?.id);
                this.getRelatedProducts(this.productDetails.id);
                const newItems = [
                    { text: 'Home', href: '/', disabled: false },
                    { text: 'Tienda', href: '/Shop', disabled: true }
                ];
                this.$store.dispatch('breadcrumb/setBreadcrumbItems', newItems);
            } else {
                this.snack({
                    message: res.data.message,
                    color: "red"
                });

                this.$router.push({ name: "404" });
            }

            this.detailsLoading = false;
        },
        async getRelatedProducts(id) {
            const res = await this.call_api("get", `product/related/${id}`);
            if (res.data.success) {
                this.relatedProducts = res.data.data;
                this.relatedLoading = false;
            }
        },
        async getMoreProducts(id) {
             //const res = await this.call_api("get", `product/random/9999/${id}`);
            const res = await this.call_api("get", `product/search?category_slug=${id}`);

            if (res.data.success) {
                // console.log("MIRAR DEBAJO DE ESTO")
                // console.log(res.data)
                res.data?.products?.data?.map(product => {
                    this.moreProducts.push({
                        id: product?.id,
                        name: product?.name,
                        val: product?.base_price + " " + product?.currency,
                        img: product?.thumbnail_image,
                        description: product?.description,
                        brand: product?.brandName,
                        ref: product?.reference,
                        slug: product?.slug,
                        category: product?.category_name,
                        tax: product?.tax
                    });
                });
            }
        },
        async getMoreProducts2(id) {
            const res = await this.call_api("get", `product/random/4/${id}`);
            if (res.data.success) {
                res.data?.data?.map(product => {
                    this.footerProducts.push({
                        id: product?.id,
                        name: product?.name,
                        val: product?.base_price + " " + product?.currency,
                        img: product?.thumbnail_image,
                        description: product?.description,
                        brand: product?.brandName,
                        ref: product?.reference,
                        slug: product?.slug
                    });
                });
            }
        },
        setVidaUtil(value) {
            if (value) {
                switch (value) {
                    case "1-10 años":
                        this.ratings.vidaUtil = 67;
                        break;
                    case "10-25 años":
                        this.ratings.vidaUtil = 100;
                        break;
                    case "1-24 Meses":
                        this.ratings.vidaUtil = 34;
                        break;
                    case "1-14 Semanas":
                        this.ratings.vidaUtil = 0;
                        break;
                    default:
                        this.ratings.vidaUtil = 0;
                        break;
                }
            }
        },
        setComposition(data) {
            this.desserts = [
                {
                    color: "background-color:#114B99",
                    name: "Plástico",
                    percent: data?.plastico,
                    kg: data?.peso_plastico
                },
                {
                    color: "background-color:#FEA60F",
                    name: "Cartón",
                    percent: data?.carton,
                    kg: data?.peso_carton
                },
                {
                    color: "background-color:#FFFFFF",
                    name: "Papel",
                    percent: data?.papel,
                    kg: data?.peso_papel
                },
                {
                    color: "background-color:#445056",
                    name: "Metal",
                    percent: data?.metal,
                    kg: data?.peso_metal
                },
                {
                    color: "background-color:#61A959",
                    name: "Vidrio",
                    percent: data?.vidrio,
                    kg: data?.peso_vidrio
                },
                {
                    color: "background-color:#B1771D",
                    name: "Madera",
                    percent: data?.madera,
                    kg: data?.peso_madera
                },
                {
                    color: "background-color:#ED847B",
                    name: "Textil",
                    percent: data?.textil,
                    kg: data?.peso_textil
                },
                {
                    color: "background-color:#ED847B",
                    name: "Baterías / Eléctrico",
                    percent: data?.bateria_electrico,
                    kg: data?.peso_bateria_electrico
                }
            ];
        },
        setImpacto(value) {
            if (value) {
                switch (value) {
                    case "Reversible":
                        this.impacto = "35%";
                        break;
                    case "Sostenible":
                        this.impacto = "11%";
                        break;
                    case "No Reversible":
                        this.impacto = "75%";
                        break;
                    default:
                        this.impacto = "11%";
                        break;
                }
            }
        }
    },
    async created() {
        this.getDetails();
    }
};
</script>

<style lang="scss" scoped>
.product-details {
    background-color: #fafcfc;

    @media (min-width: 1264px) {
        main {
            
            overflow: hidden;
        }
    }

    .v-tabs {
        &::v-deep {
            .v-slide-group__prev,
            .v-slide-group__next {
                display: none !important;
            }

            .v-tabs-bar__content {
                // gap: 0.75rem;

                // @media (min-width: 600px) {
                //     gap: 1.5rem;
                // }
            }
            max-width: 800px;
        }

        .v-tab {
            background-color: transparent;
            border-bottom: 1px solid #a5a6a8;
            color: #a5a6a8 !important;
            font-size: var(--font-size-btn);
            font-weight: 600;
            letter-spacing: 1.25px;
            line-height: 17px;
            // flex: 1; // All tabs same width

            &:not(.v-tab--active):hover {
                border-bottom: 1px solid #f78735;
                color: #f78735 !important;
            }

            &--active {
                border-bottom: 1px solid #f78735;
                color: #f78735 !important;
            }
        }
    }

    &-specs {
        overflow-x: hidden;
        padding-right: 8px;
        min-height: 50px;

    }

    &-list-box {
        border-radius: 10px;
        border: 1px solid #e4e4e4;

        &-label {
            font-size: var(--font-size-body2);
            font-weight: 600;
            line-height: 1;
            text-transform: uppercase;
        }

        &-sub-label {
            font-size: var(--font-size-body2);
            opacity: 0.5;
            font-style: italic;
        }

        &-value {
            display: block;
            font-family: "Roboto";
            font-size: var(--font-size-body1);
            letter-spacing: 0.5px;
        }

        &-list {
            li {
                font-family: "Roboto";
                font-size: var(--font-size-body1);
                letter-spacing: 0.1px;
            }
        }

        &-link {
            font-family: "Roboto";
            font-size: var(--font-size-body2);
            letter-spacing: 0.5px;
            display: block;
            text-decoration: underline;
        }

        &::v-deep {
            .v-rating {
                .v-icon {
                    color: #000000;
                    padding: 0.25rem;
                }
            }
        }
    }
}

.list-box {
    &-value {
        font-family: "Roboto", sans-serif;
        display: block;
        font-size: var(--font-size-body2);

        &.medium {
            font-size: var(--font-size-body1);
            letter-spacing: 0.1px;
        }

        &-big {
            display: block;
            font-family: "Roboto", sans-serif;
            font-size: 17px;
        }
    }

    &-caption {
        display: block;
        font-weight: 700;
        font-size: var(--font-size-caption);
        text-transform: uppercase;
        opacity: 0.5;
    }

    &-chart-label {
        font-size: var(--font-size-body1);
        font-weight: 700;

        &.small {
            font-size: var(--font-size-caption);
        }
    }

    &-chart-value {
        font-family: "Roboto", sans-serif;
        font-size: var(--font-size-caption);
    }
}

.map-pin-icon {
    top: -10%;
    position: absolute;
    left: 23%;
}

.piechart {
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background-image: conic-gradient(from 180deg, #b1771d 0 320deg, #445056 0 336deg, #114b99 0);
}
</style>
